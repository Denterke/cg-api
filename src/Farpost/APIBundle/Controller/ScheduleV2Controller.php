<?php

namespace Farpost\APIBundle\Controller;

use Farpost\APIBundle\Controller\ScheduleController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ScheduleV2Controller extends ScheduleController {

    /**
     * Returns schedule for group_id
     * Added: [2.0]
     * @param  Request $request
     * @return  Response
     */
    public function getScheduleAction(Request $request) {
        $response = $this->_createResponse();
        $result = $this->getDoctrine()->getManager()->getRepository('FarpostStoreBundle:Schedule')
                       ->getForGroup($request->query->getInt('group', 0));
        $response->setContent(json_encode(
            ['schedules' => $result]
        ))
            ->setStatusCode(200);
        return $response;
    }

    /**
     * Returns schedule and all entities, required by schedule, for group_id
     * Added: [2.0]
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
            'professors'  => 'User',
            'disciplines' => 'Discipline',
            'auditories'  => 'GeoObject',
            'times'       => 'Time',
            'schedule'    => 'Schedule'
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
        return $response->setStatusCode(200)
                        ->setContent(json_encode($result));
    }
}
