<?php

namespace Farpost\APIBundle\Controller;

use Farpost\APIBundle\Controller\APIV1Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Farpost\APIBundle\Services\CollisionFixer;

class APIV2Controller extends APIV1Controller
{
    /**
     * Fake login action
     * Added: [1.0]
     * Required: [Client 1.0]
     * @return Response
     */
    public function loginAction()
    {
        return parent::loginAction();
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
        return parent::listAction($name);
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
        $response = null;
        $result = [];
        $fatal = $this->get('collision_fixer')->wipeCheck($request, $response, $result);
        $helper = $this->get('api_helper');
        $em = $this->getDoctrine()->getManager();
        $t = $result['timestamp'];
        $result["groups"] = $em->getRepository('FarpostStoreBundle:Group')
            ->getList($t);
        $result["timestamp"] = $helper->getTimestamp();
        $response->setContent(json_encode($result))
            ->setStatusCode(200);
        return $response;
    }

    /**
     * Return values for specified group_id
     * Added: [1.0]
     * Required: [Client 1.0]
     * @param  string $name
     * @return Response
     */
    public function getForGroupAction($name)
    {
        return parent::getForGroupAction($name);
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
        return parent::getTypesAction($name);
    }

    /**
     * Get entities updates for group_id
     * Added: [2.0]
     * Required: [Client 2.0]
     * @param  Request $request
     * @return Response
     */
    public function getUpdatesAction(Request $request)
    {
        $helper = $this->get('api_helper');
        $response = null;
        $result = [];
        $fatal = $this->get('collision_fixer')->usualCheck($request, $response, $result);
        if (!$fatal) {
            $dt = new \DateTime;
            $dt->setTimestamp($request->query->getInt('t', 0));
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
                    ->getUpdate($dt, $result['group_id']);
                if (empty($elem))
                    continue;
                $result[$table_name] = $elem;
            }
            $response->setContent(json_encode($result))
                ->setStatusCode(200);            
        }
        return $response;
    }

    /**
     * Get catalog db and mbtiles updates
     * Added: [1.0]
     * Required: [Client 1.0]
     * @return Response
     */
    public function getBaseUpdatesAction()
    {
        return parent::getBaseUpdatesAction();
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
        return parent::getDepartmentsAction($request);
    }

    /**
     * Returns schedule for group_id
     * Added: [2.0]
     * Required: [Client 2.0]
     * @param  Request $request
     * @return  Response
     */
    public function getScheduleAction(Request $request) {
        $response = null;
        $result = [];
        $fatal = $this->get('collision_fixer')->usualCheck($request, $response, $result);
        if (!$fatal) {
            $result['schedules'] = $this->getDoctrine()
                ->getManager()
                ->getRepository('FarpostStoreBundle:Schedule') 
                ->getScheduleRendered($result['group_id']);
            $response->setContent(json_encode($result))->setStatusCode(200);
        }
        return $response;
    }

        /**
     * Returns schedule and all entities, required by schedule, for group_id
     * Added: [2.0]
     * Required: [Client 2.0]
     * @param  Request $request
     * @return Response
     */
    public function getFullScheduleAction(Request $request)
    {
        $response= null;
        $result = [];
        $fatal = $this->get('collision_fixer')->usualCheck($request, $response, $result);
        if (!$fatal) {
            $gId = $result['group_id'];
            $entities = [
                'professors'  => 'User',
                'disciplines' => 'Discipline',
                'auditories'  => 'GeoObject',
                'times'       => 'Time',
            ];
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
            $response->setContent(json_encode($result))->setStatusCode(200);
        }
        return $response;
    }

    /**
     * Returns news, from $news_id to $news_id + $cnt (cnt may be < 0)
     * Added: [2.0]
     * Required: [Client 2.0]
     * @param Request $request
     * @return Response 
     */
    public function getNewsAction(Request $request)
    {
        $helper = $this->get('api_helper');
        $response = $helper->create404();
        $newsId = $request->query->getInt('news_id', -1);
        $count = $request->query->getInt('count', 10);
        $result = $this->getDoctrine()
            ->getManager()
            ->getRepository('FarpostStoreBundle:News')
            ->getNews($newsId, $count, $this->getRequest()->getHost());
        if ($result === null) {
            return $response;
        }
        return $response->setContent(
            json_encode([
                'news' => $result,
                'timestamp' => $helper->getTimestamp()
            ])
        )->setStatusCode(200);
    }
}
