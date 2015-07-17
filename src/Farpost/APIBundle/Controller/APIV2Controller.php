<?php

namespace Farpost\APIBundle\Controller;

use Farpost\APIBundle\Controller\APIV1Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class APIV2Controller extends APIV1Controller
{
    /**
     * Get group list after timestamp = t
     * Added: [1.0]
     * Required: [Client 1.0]
     * @param  Request $request
     * @return Response
     */
    public function getGroupsAction(Request $request)
    {
        return parent::getGroupsAction($request);
    }

    /**
     * Get update flag for group_id
     * Added: [2.0]
     * Required: [Client 2.0]
     * @param  Request $request
     * @return Response
     */
    public function getUpdatesAction(Request $request)
    {
        $helper = $this->get('api_helper');
        $response = $helper->create404();
        if (!$request->query->has('group')) {
            return $response
                ->setStatusCode(400)
                ->setContent(json_encode([
                    'msg' => 'No group_id found in request'
                ]));
        }
        $result = [
            'update' => $this->getDoctrine()
                ->getManager()
                ->getRepository("FarpostStoreBundle:Group")
                ->checkUpdate(
                    $request->query->getInt('t', 0),
                    $group = $request->query->getInt('group', 0)
                ),
            'timestamp' => $helper->getTimestamp()
        ];
        return $response->setContent(json_encode($result))
            ->setStatusCode(200);
    }

    /**
     * Get catalog db and mbtiles updates
     * Added: [1.0]
     * Required: [Client 1.0]
     * @param Request
     * @return Response
     */
    public function getBaseUpdatesAction(Request $request)
    {
        return parent::getBaseUpdatesAction($request);
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
        return parent::getFullScheduleAction($request);
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
        $count = abs($request->query->getInt('count', 10));
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
