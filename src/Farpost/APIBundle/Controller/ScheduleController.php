<?php

namespace Farpost\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Farpost\StoreBundle\Entity;
use Doctrine\ORM\Query\Expr\Join;
use Farpost\StoreBundle\Entity\ScheduleRendered;

class ScheduleController extends Controller
{
   private function _createResponse()
   {
      return new Response('Not found', 404, ['Content-Type' => 'application/json']);
   }

   private function _generateSchedule(&$schedule)
   {
      $schedule_rendered = $schedule->getScheduleRendered();
      $current_time = $schedule->getTimeStart();
      $end_time = $schedule->getTimeEnd();
      $period = $schedule->getPeriod();
      $em = $this->getDoctrine()->getManager();
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
            $current_time->add(new \DateInterval('P' . $period . 'D'));
            $idx++;
            continue;
         }
         $idx++;
         $schedule_elem = new ScheduleRendered;
         $schedule_elem->setExecDate($current_time)
                       ->setSchedule($schedule);
         $em->persist($schedule_elem);
         $current_time->add(new \DateInterval('P' . $period . 'D'));
      }
      $em->flush();
   }

   public function renderScheduleAction()
   {
      $response = $this->_createResponse();
      $schedules = $this->getDoctrine()->getManager()
                       ->getRepository('FarpostStoreBundle:Schedule')
                       ->createQueryBuilder('sc')
                       ->getQuery()
                       ->getResult();
      foreach($schedules as &$schedule) {
         echo $schedule->getId() . "\n";
         $this->_generateSchedule($schedule);
      }
      $response->setStatusCode(200)->setContent('rendering finished!');
      return $response;
   }

   public function getScheduleAction()
   {
      $request = Request::createFromGlobals();
      $response = $this->_createResponse();
      // $items = $this->getDoctrine()->getManager()
                    // ->getRepository('FarpostStoreBundle:ScheduleRendered')
                    // ->createQueryBuilder('sr')
                    // ->innerJoin('FarpostStoreBundle:Schedule', 'sc', Join::WITH, 'sr.schedule = sc.id')
                    // ->innerJoin('FarpostStoreBundle:SchedulePart', 'sp', Join::WITH, 'sc.schedule_part = sp.id')
                    // ->innerJoin('FarpostStoreBundle:Group', 'g', Join::WITH, 'sp.group = g.id')
                    // ->where('g.id = ?1')
                    // ->andWhere('sc.time_start <= CURRENT_DATE()')
                    // ->andWhere('sc.time_end >= CURRENT_DATE()')
                    // ->setParameter(1, $request->query->getInt('group', 0))
                    // ->getQuery()
                    // ->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true)
                    // ->getResult();
      // $result = [];
      // foreach ($items as &$elem) {
         // $schedule_elem = [
            // "group_id"       => $elem->getSchedule()->getSchedulePart()->getGroup()->getId(),
            // "lesson_type_id" => $elem->getSchedule()->getLessonType()->getId(),
            // "discipline_id"  => $elem->getSchedule()->getSchedulePart()->getDiscipline()->getId(),
            // "time_id"        => $elem->getSchedule()->getTime()->getId(),
            // "auditory_id"    => $elem->getSchedule()->getAuditory()->getId(),
            // "professor_id"   => $elem->getSchedule()->getSchedulePart()->getProfessor()->getId(),
            // "status"         => 0,
            // "date"           => $elem->getExecDate()->getTimestamp(),
            // "id"             => $elem->getId()
         // ];
//
         // array_push($result, $schedule_elem);
         // print_r($schedule_elem);
      // }
      // $result = [];
      // $fake_id = 1;
      // foreach ($items as &$elem) {
      //    $current_time = $elem->getTimeStart();
      //    $end_time = $elem->getTimeEnd();
      //    $period = $elem->getPeriod();
      //    $schedule_elem = [
      //       "group_id"       => $elem->getSchedulePart()->getGroup()->getId(),
      //       "lesson_type_id" => $elem->getLessonType()->getId(),
      //       "discipline_id"  => $elem->getSchedulePart()->getDiscipline()->getId(),
      //       "time_id"        => $elem->getTime()->getId(),
      //       "auditory_id"    => $elem->getAuditory()->getId(),
      //       "professor_id"   => $elem->getSchedulePart()->getProfessor()->getId(),
      //       "status"         => 0
      //    ];
      //    while ($current_time <= $end_time) {
      //       $schedule_elem["date"] = $current_time->getTimestamp();
      //       $schedule_elem["id"] = $fake_id;
      //       $fake_id++;
      //       $current_time->add(new \DateInterval('P' . $period . 'D'));
      //       array_push($result, $schedule_elem);
      //    }
      // }
      $result = $this->getDoctrine()->getManager()->getRepository('FarpostStoreBundle:ScheduleRendered')
                     ->getForGroup($request->query->getInt('group', 0));
      $response->setContent(json_encode(
                              ['schedules' => $result]
                              // JSON_UNESCAPED_UNICODE
                           ))
               ->setStatusCode(200);
      return $response;
   }
}
