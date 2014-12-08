<?php
namespace Farpost\WebBundle\Controller;

use Farpost\StoreBundle\Daemons\Astarot;
use Farpost\StoreBundle\Entity\Document;
use Farpost\WebBundle\Form\DepartmentType;
use Farpost\WebBundle\Form\SchoolType;
use Farpost\WebBundle\Form\SpecializationViewType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    private function _getRep($table)
    {
        return $this->getDoctrine()->getRepository(
            'FarpostStoreBundle:' .
            $this->get('entity_dispatcher')->tableToEntity($table)
        );
    }

    function unlinkRecursive($dir, $deleteRootToo)
    {
        if(!$dh = @opendir($dir)) {
            return;
        }
        while (false !== ($obj = readdir($dh))) {
            if ($obj == '.' || $obj == '..') {
                continue;
            }
            if (!@unlink($dir . '/' . $obj)) {
                $this->unlinkRecursive($dir.'/'.$obj, true);
            }
        }
        closedir($dh);
        if ($deleteRootToo) {
            @rmdir($dir);
        }
        return;
    } 

    private function _clearTmp()
    {
        $tmpFiles = scandir(TEMP_DIR);
        $this->unlinkRecursive(TEMP_DIR, false);
    }

    private function _isValidForm(&$form, $request)
    {
        return $form->handleRequest($request)->isValid();
    }

    public function deleteAction(Request $request)
    {
        if (!($request->query->has('entity') && $request->query->has('id'))) {
            return $this->redirect($this->generateUrl('admin_index'));
        }
        $em = $this->getDoctrine()->getManager();
        $obj = $this->_getRep($request->get('entity'))->findOneBy(['id' => $request->get('id')]);
        if (!empty($obj)) {
            $em->remove($obj);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('admin_' . $request->get('entity')));
    }

    public function indexAction()
    {
        return $this->render('FarpostWebBundle:Admin:login.html.twig');
    }

    public function scheduleAction()
    {
        return $this->render('FarpostWebBundle:Admin:schedule.html.twig');
    }

    public function changeAction(Request $request, $isAdd)
    {
        if (!$request->query->has('entity') || (!$isAdd && !$request->query->has('id'))) {
            return $this->redirect($this->generateUrl('admin_index'));
        }
        switch ($request->query->get('entity')) {
            case 'departments':
                return $this->_departmentsChange($request, $isAdd);
                break;
            case 'versions':
                return $this->_versionAdd($request);
                break;
            case 'ssources':
                return $this->_ssourceAdd($request);
                break;
            default:
                $this->redirect($this->generateUrl('admin_index'));
                break;
        }
        return new Response('Not found', 404, ['Content-Type' => 'application/json']);
    }

    public function _departmentsChange($request, $isAdd)
    {
        $rep = $this->_getRep($request->get('entity'));
        $form = $this->createForm(
            new DepartmentType(),
            !$isAdd ? $rep->findOneBy(['id' => $request->query->get('id')]) : null
        );
        if ($this->_isValidForm($form, $request)) {
            $em = $this->getDoctrine()->getManager();
            $isAdd ? $em->persist($form->getData()) : $em->merge($form->getData());
            $em->flush();
            return $this->redirect($this->generateUrl('admin_edit', ['entity' => 'departments', 'id' => $form->getData()->getId()]));
        }
        return $this->render('FarpostWebBundle:Admin:departments_card.html.twig', [
            'schools' => $this->_getRep('schools')->findBy([], ['alias' => 'asc']),
            'department_form' => $form->createView(),
            'head_label' => $isAdd ? 'Добавление' : 'Редактирование'
        ]);
    }
    
    private function _versionAdd(Request $request)
    {
        $document = new Document();
        $document->setType($request->query->get('id'));
        $form = $this->createFormBuilder($document)
                     ->add('type', 'hidden')
                     ->add('file')
                     ->add('save', 'submit', ['label' => 'Сохранить'])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em_v = $this->getDoctrine()->getManager();
            $em_v->persist($document);
            $em_v->flush();
            $em_v->remove($document);
            $this->get('database_converter')->AddDb($document->getType(), $document->getAbsolutePath());
            if ($document->getType() == -20) {
                $this->get('schedule_manager')->refreshSchedule(false);
            }
            $em_v->flush();
            return $this->redirect($this->generateUrl('admin_basemanagement'));
        }
        $dt = new \DateTime();
        $promt = $request->query->get('id') == -20
        ? 'Файл каталога организаций'
        : ($request->query->get('id') == -59
            ? 'Файл карты ДВФУ'
            : 'Файл плана уровня ' . $request->query->get('id'));
        // echo $request->query->get('id');
        return $this->render('FarpostWebBundle:Admin:versions_card.html.twig', [
            'version_form' => $form->createView(), 'type' => $promt]);
    }

    private function _ssourceAdd(Request $request)
    {
        if ($request->query->get('id') == -1) {
            $promt = "Архив с расписанием";
        } else {
            $promt = "Файл расписания";
        }
        $document = new Document();
        $document->setType($request->query->get('id') ? -21 : -22);
        $form = $this->createFormBuilder($document)
                     ->add('type', 'hidden')
                     ->add('file')
                     ->add('save', 'submit', ['label' => 'Сохранить'])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($document);
            $em->flush();
            if ($document->getType() == -21) {
                try {
                    $zip = new \ZipArchive();
                    if ($zip->open($document->getAbsolutePath()) === TRUE) {
                        $this->_clearTmp();
                        $zip->extractTo(TEMP_DIR);
                        $zip->close();
                        $this->get('schedule_manager')->convertDirSchedule(
                            TEMP_DIR,
                            $document->getVDatetime()
                        );
                        $this->unlinkRecursive(TEMP_DIR, false);
                        // exit;
                        // $tmpFiles = scandir(TEMP_DIR);
                        // foreach ($tmpFiles as $tmpFile) {
                            // if ($tmpFile[0] == '.') {
                                // continue;
                            // }
                            // $this->get('schedule_manager')->convertSchedule(
                                // TEMP_DIR . '/' . $tmpFile,
                                // $document->getVDatetime()
                            // );
                            // unlink(TEMP_DIR . '/' . $tmpFile);
                        // }
                    }
                } catch (\Exception $e) {
                    throw $e;
                }
            } else {
                // echo $
                $this->get('schedule_manager')->convertSchedule(
                    $document->getAbsolutePath(),
                    $document->getVDatetime()
                );
            }
            // $this->get('schedule_manager')->startAstarot();
            // echo json_encode($_REQUEST);
            // echo json_encode($_FILES);
            // echo json_encode($_FILES);
            // exit;
            $em->remove($document);
            $em->flush();
            return $this->redirect($this->generateUrl('admin_basemanagement'));
        }
        return $this->render('FarpostWebBundle:Admin:versions_card.html.twig', [
            'version_form' => $form->createView(), 'type' => $promt]);

    }

    public function departmentsAction(Request $request)
    {
        return $this->render('FarpostWebBundle:Admin:departments_view.html.twig', [
            'departments' => $this->_getRep('departments')->findBy([], ['alias' => 'asc'])
        ]);
    }

    public function schoolsAction(Request $request)
    {
        $form = $this->createForm(new SchoolType(), null);
        if ($this->_isValidForm($form, $request)) {
            $em = $this->getDoctrine()->getManager();
            $em->merge($form->getData());
            $em->flush();
            return $this->redirect($this->generateUrl('admin_schools'));
        }
        return $this->render('FarpostWebBundle:Admin:schools.html.twig', [
            'schools' => $this->_getRep('schools')->findBy([], ['alias' => 'asc']),
            'school_edit_form' => $form->createView()
        ]);
    }

    public function specializationsAction(Request $request)
    {
        $form = $this->createForm(new SpecializationViewType(), null);
        if ($this->_isValidForm($form, $request)) {
        }
        return $this->render('FarpostWebBundle:Admin:specializations_view.html.twig', [
            'specializations' => $this->_getRep('specializations')->findBy([], ['alias' => 'asc']),
            'specializations_view_form' => $form->createView()
        ]);
    }

    public function basemanagmentAction(Request $request)
    {
        return $this->render('FarpostWebBundle:Admin:basemanagement.html.twig', [
            'versions' => $this->_getRep('versions')->getForWeb(),
            'ssources' => $this->_getRep('ssources')->getForWeb()
        ]);
    }

    public function scheduleLogAction()
    {
        $path = WEB_DIRECTORY . "/astarot_log.txt";
        if (file_exists($path)) {
            $result = file_get_contents($path);
        } else {
            $result = "file does not exist";
        }
        // Astarot::memcacheInit();
        // if (Astarot::isRunning()) {
            // $result = json_encode(Astarot::getState());
        // } else {
            // $result = "Schedule render daemon not running";
        // }
        // echo $result;
        // exit;
        return new Response($result, 200, ['Content-Type' => 'application/json']);
    }

    public function astarotStartAction()
    {
        $this->get('schedule_manager')->startAstarot();
        return new Response('Astarot summoned!', 200, ['Content-Type' => 'application/json']);
    }

    public function managementAction($actions)
    {
        for ($i = 0; $i < strlen($actions); $i++) {
            switch ($actions[$i]) {
                case '1':
                    system(WEB_DIRECTORY . "/../app/console doctrine:database:drop --force");
                    break;
                case '2':
                    system(WEB_DIRECTORY . "/../app/console doctrine:database:create");
                    break;
                case '3':
                    system(WEB_DIRECTORY . "/../app/console doctrine:generate:entities --no-backup Farpost");
                    break;
                case '4':
                    system(WEB_DIRECTORY . "/../app/console doctrine:schema:update --force --em=default");
                    break;
                case '5':
                    system(WEB_DIRECTORY . "/../app/console cache:warmup --env=prod --no-debug");
                    break;
                case '6':
                    system("rm -f " . WEB_DIRECTORY . "/static/*");
                    break;
                case '7':
                    system("rm -f " . WEB_DIRECTORY . "/uploads/documents/*");
                    break;
                case '8':
                    system("rm -f " . WEB_DIRECTORY . "/uploads/tmp/*");
                    break;
                case '9':
                    system("rm -f " . WEB_DIRECTORY . "/uploads/schedules/*");
                    break;
                case 'a':
                    system("rm -f " . WEB_DIRECTORY . "/astarot_log.txt");
                    break;
                case 'b':
                    system("php " . WEB_DIRECTORY . "/../scripts/FillDb.php");
                    break;
                case 'c':
                    system(WEB_DIRECTORY . "/../app/console doctrine:fixtures:load --no-interaction --append");
                    break;
                case 'd':
                    system(WEB_DIRECTORY . "/../app/console csmc --clear");
                    break;
                case 'e':
                    exec(WEB_DIRECTORY . "/../app/console paimon > /dev/null 2>&1 &");
                    break;
                case 'f':
                    system("rm -f " . WEB_DIRECTORY . "/paimon_log.txt");
                    break;
                case 'g':
                    system("rm -f " . WEB_DIRECTORY . "/static/newsImgs/*");
                    break;
            }
        }
        return new Response('Actions performed!', 200, ['Content-Type' => 'application/json']);
    }

    public function deleteGroupAction($group)
    {
        $em = $this->getDoctrine()->getManager();
        try {
            $group = $em->getRepository('FarpostStoreBundle:Group')->findOneById($group);
        } catch (\Exception $e) {
            return new Response('No group found!', 200, ['Content-Type' => 'application/json']);
        }
        $em->remove($group);
        $em->flush();
        return new Response('Group deleted', 200, ['Content-Type' => 'application/json']);
    }
}
