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

   public function renderScheduleAction()
   {
      $response = $this->_createResponse();
      $schedules = $this->getDoctrine()->getManager()
                       ->getRepository('FarpostStoreBundle:Schedule')
                       ->createQueryBuilder('sc')
                       ->getQuery()
                       ->getResult();
      $schedule_manager = $this->get('schedule_manager');
      foreach($schedules as &$schedule) {
         $schedule_manager->generateSchedule($schedule);
         // $this->_generateSchedule($schedule);
      }
      $response->setStatusCode(200)->setContent('rendering finished!');
      return $response;
   }

   public function getScheduleAction()
   {
      $request = Request::createFromGlobals();
      $response = $this->_createResponse();
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
