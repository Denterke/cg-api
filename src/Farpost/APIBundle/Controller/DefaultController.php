<?php

namespace Farpost\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Farpost\StoreBundle\Entity;

class DefaultController extends Controller
{

   private function _CreateResponse()
   {
      return new Response('Not found', 404, ['Content-Type' => 'application/json']);
   }

   public function indexAction($name)
   {
      $response = new Response(json_encode(['method' => $index]));
      $response->headers->set('Content-Type', 'application/json');
      return $response;
   }

   public function listAction($name)
   {
      $response = $this->_CreateResponse();
      if (!in_array($name, ['building', 'school'])) $response;
      $items = $this->getDoctrine()
                    ->getManager()
                    ->getRepository('FarpostStoreBundle:' . ucfirst($name))
                    ->findAll();
      return $response->setStatusCode(200)->setContent(json_encode($items, JSON_UNESCAPED_UNICODE));
   }

   public function getGroupsAction()
   {
      parse_str($_SERVER['QUERY_STRING']);
      $response = new Response(json_encode([
            'method' => 'groupGet',
            'school_id' => $school_id,
            'study_type_id' => $study_type_id
      ]));
      $response->headers->set('Content-Type', 'application/json');
      return $response;
   }

   public function getForGroupAction($name)
   {
      parse_str($_SERVER['QUERY_STRING']);
      $response = new Response(json_encode([
            'method' => 'getForGroup',
            'name' => $name,
            'group_id' => $group_id
      ]));
      $response->headers->set('Content-Type', 'application/json');
      return $response;
   }
}
