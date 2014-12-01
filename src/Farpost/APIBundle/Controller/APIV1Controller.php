<?php

namespace Farpost\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Farpost\StoreBundle\Entity;
use Doctrine\ORM\Query\Expr\Join;

class APIV1Controller extends Controller
{
    /**
     * Fake login action
     * Added: [1.0]
     * Required: [Client 1.0]
     * @return Response
     */
    public function loginAction()
    {
        $helper = $this->get('api_helper');
        $response = $helper->create404();
        return $response->setStatusCode(200)
            ->setContent(json_encode([
                'secret'            => 'fuck',
                'first_name'        => 'Василий',
                'last_name'         => 'Васильев',
                'middle_name'       => 'Васильевич',
                'role_id'           => 1,
                'group_id'          => 1,
                'study_type_id'     => 1,
                'school_id'         => 1,
                'specialization_id' => 1
        ]));
    }

    /**
     * Get list values (the same for all groups) for group
     * Added: [1.0]
     * Required: [Client 1.0]
     * @param  string $name
     * @return Response
     */
    public function listAction($name)
    {
        $helper = $this->get('api_helper');
        $response = $helper->create404();
        if (!in_array($name, ['building', 'school'])) {
            return $response;
        }
        $em = $this->getDoctrine()->getEntityManager();
        $items = $em->getRepository('FarpostStoreBundle:' . ucfirst($name))
            ->getList();
        return $response->setStatusCode(200)
            ->setContent(json_encode(["{$name}s" => $items]));
    }

    /**
     * Get group list after timestamp = t
     * Added: [1.0]
     * Required: [Client 1.0]
     * Replaced: [APIV2Controller::getGroupsAction, 2.0]
     * @param  Request $request
     * @return Response
     */
    public function getGroupsAction(Request $request)
    {
        $helper = $this->get('api_helper');
        $response = $helper->create404();
        $em = $this->getDoctrine()->getManager();
        $t = $request->query->has('t') ? $request->query->get('t') : 1;
        $result = $em->getRepository('FarpostStoreBundle:Group')
            ->getList($t);
        return $response->setContent(json_encode([
                        'groups' => $result,
                        'timestamp' => $helper->getTimestamp()
                ]))
            ->setStatusCode(200);
    }

    /**
     * Return values for specified group_id
     * Add: [1.0]
     * Required: [Client 1.0]
     * Replaced: [APIV2Controller::getForGroupAction, 2.0]
     * @param  string $name
     * @return Response
     */
    public function getForGroupAction($name)
    {
        $helper = $this->get('api_helper');
        $response = $helper->create404();
        $request = Request::createFromGlobals();
        $entities = [
            'times'       => 'Time',
            'auditories'  => 'GeoObject',
            'professors'  => 'User',
            'disciplines' => 'Discipline'
        ];
        if (!$request->query->has('group') || empty($entities[$name])) {
            return $response;
        }
        $en_name = $entities[$name];
        $result = $this->getDoctrine()
            ->getManager()
            ->getRepository("FarpostStoreBundle:$en_name")
            ->getForGroup($request->query->getInt('group', 0));
        return $response->setStatusCode(200)
            ->setContent(json_encode([$name => $result]));
    }

    /**
     * Get type values (the same for all groups)
     * Added: [1.0]
     * Required: [Client 1.0]
     * @param string $name
     * @return Response
     */
    public function getTypesAction($name)
    {
        $helper = $this->get('api_helper');
        $response = $helper->create404();
        if (!in_array($name, ['lesson', 'study', 'building'])) {
            return $response;
        }
        $items = $this->getDoctrine()
            ->getEntityManager()
            ->getRepository('FarpostStoreBundle:' . ucfirst($name) . 'Type')
            ->createQueryBuilder('a')
            ->getQuery()
            ->getArrayResult();
        return $response->setStatusCode(200)->setContent(json_encode(
            ["{$name}_types" => $items]
        ));
    }

    /**
     * Get entities updates for group_id
     * Added: [1.0]
     * Required: [Client 1.0]
     * Replaced: [APIV2Controller::getUpdatesAction, 2.0]
     * @param  Request $request
     * @return Response
     */
    public function getUpdatesAction(Request $request)
    {
        $helper = $this->get('api_helper');
        $response = $helper->create404();
        if (!$request->query->has('group') || !$request->query->has('t')) {
            return $response;
        }
        $dt = new \DateTime;
        $dt->setTimestamp($request->query->getInt('t', 0));
        $group = $request->query->getInt('group', 0);
        $result = [];
        $entities = [
            'GeoObject'  => 'auditories',
            'User'       => 'professors',
            'Time'       => 'times',
            'Discipline' => 'disciplines',
            'Schedule'   => 'schedules',
            'Group'      => 'groups'
        ];
        foreach($entities as $en_name => $table_name) {
            $elem = $this->getDoctrine()->getManager()
                ->getRepository("FarpostStoreBundle:$en_name")
                ->getUpdate($dt, $group);
            if (empty($elem))
                continue;
            $result[$table_name] = $elem;
        }
        $result['timestamp'] = $helper->getTimestamp();
        return $response->setContent(json_encode($result))
            ->setStatusCode(200);
    }

    /**
     * Get catalog db and mbtiles updates
     * Added: [1.0]
     * Required: [Client 1.0]
     * @return Response
     */
    public function getBaseUpdatesAction()
    {
        $helper = $this->get('api_helper');
        $response = $helper->create404();
        $result = [
           'update' =>  $this->getDoctrine()->getManager()
                ->getRepository('FarpostStoreBundle:Version')
                ->getBases($this->getRequest()->getHost())
            ];
        return $response->setStatusCode(200)
            ->setContent(json_encode($result));
    }

    /**
     * Get departments for school
     * Added: [1.0]
     * Required: [Client 1.0]
     * @param  Request $request
     * @return Response
     */
    public function getDepartmentsAction(Request $request)
    {
        $helper = $this->get('api_helper');
        $response = $helper->create404();
        if (!$request->query->has('school') || (!$request->get('school'))) {
            return $response;
        }            
        $result = $this->getDoctrine()
            ->getRepository('FarpostStoreBundle:Department')
            ->createQueryBuilder('d')
            ->where('d.school = :school')
            ->setParameters(['school' => $request->get('school')])
            ->getQuery()
            ->getArrayResult();
        if (!empty($result)) {
           $response->setStatusCode(200)->setContent(json_encode($result));
        }
        return $response;
    }

    /**
     * Returns schedule for group_id
     * Added: [1.0]
     * Required: [Client 1.0]
     * Replaced: [APIV2Controller::getScheduleAction(), 2.0]
     * @param  Request $request
     * @return  Response
     */
    public function getScheduleAction(Request $request) {
        $helper = $this->get('api_helper');
        $response = $helper->create404();
        $result = $this->getDoctrine()
            ->getManager()
            ->getRepository('FarpostStoreBundle:Schedule')
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
     * Required: [Client 1.0]
     * Replaced: [APIV2Controller::getFullScheduleAction(), 2.0]
     * @param  Request $request
     * @return Response
     */
    public function getFullScheduleAction(Request $request) {
        $helper = $this->get('api_helper');
        $response = $helper->create404();
        if (!$request->query->has('group')) {
            return $response;
        }
        $gId = $request->query->getInt('group', 0);
        $entities = [
            'professors'  => 'User',
            'disciplines' => 'Discipline',
            'auditories'  => 'GeoObject',
            'times'       => 'Time'
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
        $result['timestamp'] = $helper->getTimestamp();
        return $response->setStatusCode(200)
            ->setContent(json_encode($result));
    }
}
