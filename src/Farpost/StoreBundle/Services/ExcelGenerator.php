<?php
/**
 * Created by PhpStorm.
 * User: daniilre
 * Date: 25/08/15
 * Time: 21:08
 */

namespace Farpost\StoreBundle\Services;

use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_RichText;
use Symfony\Component\HttpFoundation\Response;
use Farpost\StoreBundle\Entity;


class ExcelGenerator
{
    private $excelObject;
    private $worksheet;
    private $doctrine;

    public function __construct($fileName, $doctrine)
    {
        $this->excelObject = PHPExcel_IOFactory::load($fileName);
        $this->excelObject->setActiveSheetIndex(0);
        $this->worksheet = $this->excelObject->getActiveSheet();
        $this->doctrine = $doctrine;
    }
    public function export($group_opts, $output)
    {
        mb_internal_encoding("UTF-8");
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

        foreach($group_opts as $ng => $group_opt) {
            if ($group_opt["type"] == "sub"){
                $group_opt['group'] = $this->doctrine
                    ->getRepository('FarpostStoreBundle:Group')
                    ->find($group_opt['group'])->getAlias();
                $group_opt['group'] = explode("-", $group_opt['group'])[0] . "%";
                $where = "g.alias LIKE :group";
            } else
                $where = "g.id = :group";

            $schedules = $this->doctrine->getEntityManager()
                ->createQueryBuilder()
                ->select('s')
                ->from('FarpostStoreBundle:Schedule', 's')
                ->innerJoin('s.schedule_part', 'sp')
                ->innerJoin('sp.group', 'g')
                ->where($where . ' AND s.semester = :semester')
                ->setParameter('group', $group_opt['group'])
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
            $this->worksheet->setCellValueByColumnAndRow(($ng*2) + 2, 10, $group->getAlias());
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
                    $this->worksheet->setCellValueByColumnAndRow(($ng*2) + 2, $row, $headline);
                    $this->worksheet->setCellValueByColumnAndRow(($ng*2) + 2, $row + 1, sprintf("%s %s %s %s", $professor->getDegree(), $professor->getLastName(),
                        mb_substr($professor->getFirstName(), 0, 1), mb_substr($professor->getMiddleName(), 0, 1)));
                    $this->worksheet->setCellValueByColumnAndRow(($ng*2) + 3, $row, $lesson_short);
                    $this->worksheet->setCellValueByColumnAndRow(($ng*2) + 3, $row + 1, $schedule->getAuditory() ? $schedule->getAuditory()->getAlias() : "");
                    $this->worksheet->getStyleByColumnAndRow(($ng*2) + 2, $row)->applyFromArray($descipline_style);
                    $this->worksheet->getStyleByColumnAndRow(($ng*2) + 3, $row)->applyFromArray($descipline_style);
                    if (count($discipline) > 33)
                        $this->worksheet->getRowDimension($template_map[$day] + (2 * $i))->setRowHeight(200);
                }
            }

            $objWriter = PHPExcel_IOFactory::createWriter($this->excelObject, 'Excel5');
            $objWriter->save($output);
        }
    }
}