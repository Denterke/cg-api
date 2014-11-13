<?php

namespace Farpost\APIBundle\Controller;

use Farpost\StoreBundle\Entity\ScheduleRendered;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ScheduleController extends Controller {

    /**
     * Creates "Not found" response
     * Added: [1.0]
     * @return Response
     */
    protected function _createResponse() {
        return new Response('Not found', 404, ['Content-Type' => 'application/json']);
    }

    /**
     * Generates ScheduleRendered records, for specified schedule_item
     * Added: [1.0]
     * Depricated: [1.6]
     * @param Schedule $schedule
     */
    private function _generateSchedule(&$schedule) {
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

    /**
     * Starts schedule rendering
     * Added: [1.0]
     * Depricated: [2.0]
     * @return Response
     */
/*  public function renderScheduleAction() {
        $response = $this->_createResponse();
        $schedules = $this->getDoctrine()->getManager()
                          ->getRepository('FarpostStoreBundle:Schedule')
                          ->createQueryBuilder('sc')
                          ->getQuery()
                          ->getResult();
        $schedule_manager = $this->get('schedule_manager');
        foreach ($schedules as &$schedule) {
            $schedule_manager->generateSchedule($schedule);
            // $this->_generateSchedule($schedule);
        }
        $response->setStatusCode(200)->setContent('rendering finished!');
        return $response;
    }
*/
    /**
     * Returns schedule for group_id
     * Added: [1.0]
     * Replaced: [ScheduleV2Controller::getScheduleAction(), 2.0]
     * @param  Request $request
     * @return  Response
     */
    public function getScheduleAction(Request $request) {
        $response = $this->_createResponse();
        $result = $this->getDoctrine()->getManager()->getRepository('FarpostStoreBundle:Schedule')
                       ->getScheduleRendered($request->query->getInt('group', 0));
        $response->setContent(json_encode(
            ['schedules' => $result]
        ))
            ->setStatusCode(200);
        return $response;
    }

    /**
     * Returns schedule and all entities, required by schedule, for group_id
     * Added: [1.0]
     * Replaced: [ScheduleV2Controller::getFullScheduleAction(), 2.0]
     * @param  Request $request
     * @return Response
     */
    public function getFullScheduleAction(Request $request) {
        $response = $this->_createResponse();
        if (!$request->query->has('group')) {
            return $response;
        }
        $gId = $request->query->getInt('group', 0);
        $entities = [
            'professors' => 'User',
            'disciplines' => 'Discipline',
            'auditories' => 'GeoObject',
            'times' => 'Time'
        ];
        $result = [];
        $em = $this->getDoctrine()->getManager();
        foreach ($entities as $name => $entity) {
            $result[$name] = $em->getRepository("FarpostStoreBundle:$entity")
                                ->getForGroup($gId);
        }
        $result['lesson_types'] = $em->getRepository('FarpostStoreBundle:LessonType')
                                     ->createQueryBuilder('a')
                                     ->getQuery()
                                     ->getArrayResult();
        $result['schedule'] = $em->getRepository('FarpostStoreBundle:Schedule')
                                 ->getScheduleRendered($gId);
        return $response->setStatusCode(200)
                        ->setContent(json_encode($result));
    }
}
