<?php

namespace Farpost\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Farpost\StoreBundle\Entity;

class DefaultController extends Controller
{

   public function indexAction($name)
   {
      $response = new Response(json_encode(['method' => $index]));
      $response->headers->set('Content-Type', 'application/json');
      return $response;
   }

   public function listAction($name)
   {
      $response = new Response('Not found', 404, ['Content-Type' => 'application/json']);
      if (in_array($name, ['building', 'school'])) {
         $items = $this->getDoctrine()
                       ->getManager()
                       ->getRepository('FarpostStoreBundle:' . ucfirst($name))
                       ->findAll();
         $response->setContent(json_encode($items))
                  ->headers->set('Content-Type', 'application/json');
      }
      return $response;
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
