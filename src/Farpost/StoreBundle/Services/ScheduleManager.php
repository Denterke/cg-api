<?php
namespace Farpost\StoreBundle\Services;
use Farpost\StoreBundle\Entity;
use Farpost\StoreBundle\Entity\Schedule;
use Farpost\StoreBundle\Entity\ScheduleSource;
use Farpost\StoreBundle\Entity\ScheduleRendered;
use Farpost\StoreBundle\Entity\School;
use Farpost\StoreBundle\Entity\StudyType;
use Farpost\StoreBundle\Entity\Course;
use Farpost\StoreBundle\Entity\Department;
use Farpost\StoreBundle\Entity\Specialization;
use Farpost\StoreBundle\Entity\Document;

class ScheduleManager
{
   private $doctrine;

   public function __construct($doctrine)
   {
      $this->doctrine = $doctrine;
   }

   public function generateSchedule(Schedule $schedule)
   {
      $schedule_rendered = $schedule->getScheduleRendered();
      $current_time = clone $schedule->getSemester()->getTimeStart();
      $dow = $schedule->getDay();
      $current_dow = date("N", $current_time->getTimestamp());
      $period = $schedule->getPeriod();
      $end_time = $schedule->getSemester()->getTimeEnd();
      if ($dow < $current_dow) {
         $dow += $period;
         $current_time = $current_time->add(new \DateInterval('P' . $period . 'D'));
      }
      while ($dow != $current_dow) {
         $current_dow++;
         $current_time = $current_time->add(new \DateInterval('P' . 1 . 'D'));
      }
      $em = $this->doctrine->getManager('default');
      $idx = 0;
      while ($current_time <= $end_time) {
         $qb = $em->createQueryBuilder();
         if ($idx < $schedule_rendered->count()) {
            $result = $qb->update('FarpostStoreBundle:ScheduleRendered', 'sr')
                         ->set('sr.exec_date', ':time')
                         ->where('sr.id = :id')
                         ->setParameter('id', $schedule_rendered[$idx]->getId())
                         ->setParameter('time', $current_time)
                         ->getQuery()
                         ->execute();
            if (!$result) {
               die("WHAT THE FUCK IS GOING ON????! I CANNT DO UPDATE!!!!");
            }
            $current_time = $current_time->add(new \DateInterval('P' . $period . 'D'));
            $idx++;
            $em->flush();
            continue;
         }
         $schedule_elem = new ScheduleRendered();
         $schedule_elem->setExecDate($current_time)
                       ->setSchedule($schedule);
         $em->persist($schedule_elem);
         $em->flush();
         $current_time = $current_time->add(new \DateInterval('P' . $period . 'D'));
      }
   }

   private function syncGroupInfo($group_info)
   {
      $em = $this->doctrine->getManager('default');
      try {
         // echo $group_info;
         list(
            $_school,
            $_group,
            $_study_type,
            $_course,
            $_department,
            $_spec
         ) = explode(";", $group_info);
         // $group_str_refs = explode(";", $group_info);
      }
      catch (\Exception $e) {
         throw new \Exception("Can not split group info string: " . $e->getMessage());
      }
      // $group_refs = [];
      // for ($i = 0; $i < count($group_info_entities); $i++) {
         // $entity_name = 'FarpostStoreBundle:' . $group_info_entities[$i];
         // $group_refs_str[$entity_name] = $em->getRepository($entity_name)
                                            // ->syncValue($group_refs_str[$i]);
      // }
      $school = $em->getRepository('FarpostStoreBundle:School')
                   ->syncValue($_school);
      $study_type = $em->getRepository('FarpostStoreBundle:StudyType')
                   ->syncValue($_study_type);
      $specialization = $em->getRepository('FarpostStoreBundle:Specialization')
                   ->syncValue($_spec);
      $course = $em->getRepository('FarpostStoreBundle:Course')
                   ->syncValue($_course);
      $department = $em->getRepository('FarpostStoreBundle:Department')
                   ->syncValue($_department, $school, $study_type);
      $study_set = $em->getRepository('FarpostStoreBundle:StudySet')
                   ->syncValue($specialization, $course, $department);
      $group = $em->getRepository('FarpostStoreBundle:Group')
                   ->syncValue($_group, $study_set);
      return $group;
   }

   public function convertSchedule($path, $vdatetime, $createSS = true)
   {
      error_log("in coverter i am");
      $em = $this->doctrine->getManager('default');
      $group_info_entities = ['School', 'Group', 'StudyType', 'Course', 'Department', 'Specialization'];
      $ss_file = fopen($path, 'r');
      if (!$ss_file) {
         throw new \Exception("Can not open file: <$path>");
      }
      $group_info = fgets($ss_file);
      $str_num = 1;
      $group = $this->syncGroupInfo($group_info);
      while (!feof($ss_file)) {
         $schedule_template = fgets($ss_file);
         if (rtrim($schedule_template) == '') {
            break;
         }
         try {
            list(
               $_l_num,
               $_discipline,
               $_l_type,
               $_professor,
               $_geoobject,
               $period,
               $day
            ) = explode(";", $schedule_template);
         }
         catch(\Exception $e) {
            throw new \Exception("Can not split schedule template string #$str_num: " . $e->getMessage());
         }
         $str_num++;
         $i = 0;

         $entities = [
            'Time' => $_l_num,
            'Discipline' => $_discipline,
            'LessonType' => $_l_type,
            'User' => $_professor,
            'GeoObject' => $_geoobject
         ];
         foreach($entities as $en_name => &$entity) {
            $entity = $em->getRepository('FarpostStoreBundle:' . $en_name)
                         ->syncValue($entity);
         }
         $schedule_part = $em->getRepository('FarpostStoreBundle:SchedulePart')
                             ->syncValue($entities['User'], $entities['Discipline'], $group);
         $schedule = $em->getRepository('FarpostStoreBundle:Schedule')->doUpdate(
            $period,
            $schedule_part,
            $entities['GeoObject'],
            $entities['Time'],
            $entities['LessonType'],
            $day,
            $group
         );
         $this->generateSchedule($schedule);
      }
      if ($createSS) {
         $ssource = new ScheduleSource();
         $ssource->setVDatetime($vdatetime)
                 ->setBase($path)
                 ->setGroup($group);
         $em->persist($ssource);
         $em->flush();
      }
   }

   public function refreshSchedule()
   {
      echo "refreshSchedule here";
      $schedule_templates = $this->doctrine->getManager('default')
         ->getRepository('FarpostStoreBundle:ScheduleSource', 'ssrc')
         ->getLastRecords();
      echo json_encode($schedule_templates);
      foreach($schedule_templates as &$s_template) {
         echo "here!";
         $this->convertSchedule(
            $s_template->getBase(),
            $s_template->getVDatetime(),
            false
         );
      }
   }
}
