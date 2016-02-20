<?php

namespace Farpost\APIBundle\Controller;

//use Farpost\APIBundle\Controller\APIV1Controller;
//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Farpost\FeedbackBundle\Entity\Feedback;
use Farpost\POIBundle\Serializer\GroupSerializer;
use Farpost\POIBundle\Serializer\PointSerializer;
use Farpost\POIBundle\Serializer\TypeSerializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
        $baseTimestamp = $request->query->get('last_timestamp', null);
        $baseDatetime = $baseTimestamp
            ? (new \DateTime())->setTimestamp($baseTimestamp)
            : null
        ;
        $count = abs($request->query->getInt('count', 10));
        $articles = $this->getDoctrine()
            ->getManager()
            ->getRepository('FarpostNewsBundle:Article')
            ->getList($baseDatetime, $count);
        $result = $this->get('farpost_news.serializer.article')->serialize($articles);

        return $response->setContent(
            json_encode([
                'news' => $result,
                'timestamp' => $helper->getTimestamp()
            ])
        )->setStatusCode(200);
    }

    /**
     * Returns poi type groups
     * Added [3.0]
     * Required [Client 3.0]
     * @param Request $request
     * @return Response
     */
    public function getPOIGroupsAction(Request $request)
    {
        $helper = $this->get('api_helper');
        $response = $helper->create404();

        $groupsRepository = $this->getDoctrine()
            ->getManager()
            ->getRepository('FarpostPOIBundle:Group')
        ;

        $id = $request->query->get('id', null);

        $groups = $id
            ? $groupsRepository->findBy(['id' => $id])
            : $groupsRepository->findAll()
        ;

        $result = $this->get('farpost_poi.serializer.group')->serialize($groups, GroupSerializer::FULL_CARD);

        return $response->setContent(json_encode($result))->setStatusCode(200);
    }

    /**
     * Returns poi types
     * Added [3.0]
     * Required [Client 3.0]
     * @param Request $request
     * @return Response
     */
    public function getPOITypesAction(Request $request)
    {
        $helper = $this->get('api_helper');
        $response = $helper->create404();

        $typesRepository = $this->getDoctrine()
            ->getManager()
            ->getRepository('FarpostPOIBundle:Type')
        ;

        $id = $request->query->get('id', null);
        $groupId = $request->query->get('groupId', null);

        $types = $id
            ? $typesRepository->findBy(['id' => $id])
            : ($groupId
                ? $typesRepository->findBy(['group' => $groupId])
                : $typesRepository->findAll()
            )
        ;

        $result = $this->get('farpost_poi.serializer.type')->serialize($types, TypeSerializer::FULL_CARD);

        return $response->setContent(json_encode($result))->setStatusCode(200);
    }

    /**
     * Returns pois
     * Added [3.0]
     * Required [Client 3.0]
     * @param Request $request
     * @return Response
     */
    public function getPOIPointsAction(Request $request)
    {
        $helper = $this->get('api_helper');
        $response = $helper->create404();

        $pointsRepository = $this->getDoctrine()
            ->getManager()
            ->getRepository('FarpostPOIBundle:Point')
        ;

        $id = $request->query->get('id', null);
        $typeId = $request->query->get('typeId', null);
        $groupId = $request->query->get('groupId', 1);

        if ($id !== null) {
            $points = $pointsRepository->findBy(['id' => $id]);
        } else if ($typeId !== null) {
            $points = $pointsRepository->findActualByType($typeId);
        } else if ($groupId !== null) {
            $points = $pointsRepository->findActualByTypeGroup($groupId);
        } else {
            $points = $pointsRepository->findActualAll();
        }

        $result = $this->get('farpost_poi.serializer.point')->serialize($points, PointSerializer::FULL_CARD);

        return $response->setContent(json_encode($result))->setStatusCode(200);
    }

    /**
     * Add feedback
     * Added [3.0]
     * Required [Client 3.0]
     * @param Request $request
     * @return Response
     */
    public function addFeedbackAction(Request $request)
    {
        $helper = $this->get('api_helper');
        $response = $helper->create404();

        $username = trim($request->request->get('username'));
        $phone = trim($request->request->get('phone'));
        $message = trim($request->request->get('message'));

        if ($username || $phone || $message) {
            $feedback = new Feedback();
            $feedback
                ->setUsername($username)
                ->setMessage($message)
                ->setPhone($phone)
            ;
            $em = $this->getDoctrine()->getManager();
            $em->persist($feedback);
            $em->flush();
        }

        return $response->setStatusCode(200)->setContent(json_encode(['status' => 'ok']));
    }
}
