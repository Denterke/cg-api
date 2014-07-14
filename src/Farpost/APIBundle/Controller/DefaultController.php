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
      // if (!in_array($name, ['building', 'school'])
      $repository = $this->getDoctrine()->getManager()
         ->getRepository('FarpostStoreBundle:' . ucfirst($name));
      $items = $repository->findAll();
      $response = new Response(json_encode($items));
      $response->headers->set('Content-Type', 'application/json');
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
