<?php

namespace Farpost\StoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_RichText;
use Symfony\Component\HttpFoundation\Response;
use Farpost\StoreBundle\Entity;


class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('FarpostStoreBundle:Default:index.html.twig', array('name' => $name));
    }

    public function generateExcelAction()
    {
        $request = Request::createFromGlobals();
        $objPHPExcel = PHPExcel_IOFactory::load(__DIR__ . "/../Resources/schedule.xls");
        $objPHPExcel->setActiveSheetIndex(0);
        $worksheet = $objPHPExcel->getActiveSheet();

        // Maps day of week with position in xls sheet
        $template_map = array(
            1 => 13,
            2 => 25,
            3 => 37,
            4 => 49,
            5 => 61,
            6 => 73,
        );

        $descipline_style = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'size'=> 12,
                'bold'=> true
            )
        );

        foreach($request->query->get('groups') as $ng => $group) {
            $schedules = $this->getDoctrine()->getEntityManager()
                ->createQueryBuilder()
                ->select('s')
                ->from('FarpostStoreBundle:Schedule', 's')
                ->innerJoin('s.schedule_part', 'sp')
                ->innerJoin('sp.group', 'g')
                ->where('g.id = :group AND s.semester = :semester')
                ->setParameter('group', $group)
                ->setParameter('semester', 2)
                ->getQuery()
                ->getResult();

            $days = array(
                1 => array(),
                2 => array(),
                3 => array(),
                4 => array(),
                5 => array(),
                6 => array(),
            );
            $group = $schedules[0]->getSchedulePart()->getGroup();
            $worksheet->setCellValueByColumnAndRow(($ng*2) + 2, 10, $group->getAlias());
            foreach ($schedules as $schedule) {
                $dayindex = $schedule->getDay();
                if ($dayindex > 7) {
                    $dayindex = $dayindex - 7;
                }
                array_push($days[$dayindex], $schedule);
            }
            foreach ($days as $day => $lessons) {
                foreach ($lessons as $i => $schedule) {

                    // Setting separate style for discipline ...
                    $headline = new PHPExcel_RichText();
                    $discipline = $headline->createTextRun($schedule->getSchedulePart()->getDiscipline()->getAlias());
                    $discipline->getFont()->setBold(true);
                    $discipline->getFont()->setName("Times New Roman");
                    $discipline->getFont()->setSize(12);
                    // ... and period type
                    if ($schedule->getPeriod() == 14) {
                        $weektype = $schedule->getDay() < 7 ? "ч" : "неч";
                        $weektype = $headline->createTextRun(" (" . $weektype . ". нед)");
                        $weektype->getFont()->setItalic(true);
                        $weektype->getFont()->setName("Times New Roman");
                        $weektype->getFont()->setSize(10);
                    }
                    $professor = $schedule->getSchedulePart()->getProfessor();
                    $row = $template_map[$day] + (2 * ($schedule->getTime()->getId() - 1));

                    if ($schedule->getLessonType())
                        $lesson_short = join("", array_map(function ($k) {
                            return mb_strtoupper(mb_substr($k, 0, 1));
                        }, explode(" ", $schedule->getLessonType()->getAlias())));
                    else
                        $lesson_short = "";
                    $worksheet->setCellValueByColumnAndRow(($ng*2) + 2, $row, $headline);
                    $worksheet->setCellValueByColumnAndRow(($ng*2) + 2, $row + 1, sprintf("%s %s %s %s", $professor->getDegree(), $professor->getLastName(),
                        mb_substr($professor->getFirstName(), 0, 1), mb_substr($professor->getMiddleName(), 0, 1)));
                    $worksheet->setCellValueByColumnAndRow(($ng*2) + 3, $row, $lesson_short);
                    $worksheet->setCellValueByColumnAndRow(($ng*2) + 3, $row + 1, $schedule->getAuditory()->getAlias());
                    $worksheet->getStyleByColumnAndRow(($ng*2) + 2, $row)->applyFromArray($descipline_style);
                    $worksheet->getStyleByColumnAndRow(($ng*2) + 3, $row)->applyFromArray($descipline_style);
                    if (count($discipline) > 33)
                        $worksheet->getRowDimension($template_map[$day] + (2 * $i))->setRowHeight(200);
                }
            }
        }
        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', 'attachment;filename="fefu_s.xls');
        $response->sendHeaders();

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit();
    }
}
