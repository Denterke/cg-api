<?php
namespace Farpost\WebBundle\Controller;

use Farpost\StoreBundle\Entity\Version;
use Farpost\StoreBundle\Entity\Document;
use Farpost\StoreBundle\Entity\ScheduleSource;
use Farpost\WebBundle\Form\SchoolType;
use Farpost\WebBundle\Form\DepartmentType;
use Farpost\WebBundle\Form\SpecializationType;
use Farpost\WebBundle\Form\SpecializationViewType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
   private function _getRep($table)
   {
      return $this->getDoctrine()->getRepository(
         'FarpostStoreBundle:' .
         $this->get('entity_dispatcher')->tableToEntity($table)
      );
   }

   private function _clearTmp()
   {
      $tmpFiles = scandir(TEMP_DIR);
      foreach($tmpFiles as $tmpFile) {
         if ($tmpFile[0] == '.') {
            continue;
         }
         unlink(TEMP_DIR . '/' . $tmpFile);
      }
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
         // $document->upload();
         $em_v->persist($document);
         $em_v->flush();
         $em_v->remove($document);
         // echo $document->getAbsolutePath();
         $this->get('database_converter')->AddDb($document->getType(), $document->getAbsolutePath());
         if ($document->getType() == -20) {
            $this->get('schedule_manager')->refreshSchedule();
         }
         $em_v->flush();
         // print_r($_POST);
         // exit;
         // $em->refresh($document)
         
         return $this->redirect($this->generateUrl('admin_basemanagement'));
      }
      $dt = new \DateTime();
      // echo $dt->getTimestamp();
      $promt = $request->query->get('id') == -20 
               ? 'Файл каталога организаций'
               : ($request->query->get('id') == -59
                  ? 'Файл карты ДВФУ'
                  :'Файл плана уровня ' . $request->query->get('id'));
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
         $em->remove($document);
         if ($document->getType() == -21) {
            try {
               $zip = new \ZipArchive();
               if ($zip->open($document->getAbsolutePath()) === TRUE) {
                  $this->_clearTmp();
                  $zip->extractTo(TEMP_DIR);
                  $zip->close();
                  // exit;
                  $tmpFiles = scandir(TEMP_DIR);
                  foreach($tmpFiles as $tmpFile) {
                     if ($tmpFile[0] == '.') {
                        continue;
                     }
                     $this->get('schedule_manager')->convertSchedule(
                        TEMP_DIR . '/' . $tmpFile,
                        $document->getVDatetime()
                     );
                     unlink(TEMP_DIR . '/' . $tmpFile);
                  }
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
         // echo json_encode($_REQUEST);
         // echo json_encode($_FILES);
         // echo json_encode($_FILES);
         // exit;
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
      // phpinfo();
      return $this->render('FarpostWebBundle:Admin:basemanagement.html.twig', [
         'versions' => $this->_getRep('versions')->getForWeb(),
         'ssources' => $this->_getRep('ssources')->getForWeb()
      ]);
   }
}
