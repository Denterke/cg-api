<?php

namespace Farpost\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Farpost\StoreBundle\Entity;
use Doctrine\ORM\Query\Expr\Join;

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
      $request = Request::createFromGlobals();
      $response = $this->_CreateResponse();
      $entities = [
         'times'       => ['c' => 'Time',       'jf' => 'time',       'f' => ['end_time', 'start_time']],
         'auditories'  => ['c' => 'Auditory',   'jf' => 'auditory',   'f' => ['alias', 'building_id', 'level_id', 'type_id']],
         'professors'  => ['c' => 'User',       'jf' => 'professor',  'f' => ['first_name', 'middle_name', 'last_name']],
         'disciplines' => ['c' => 'Discipline', 'jf' => 'discipline', 'f' => ['alias']]
      ];
      if (!$request->query->has('group') || empty($entities[$name])) return $response;
      $table = $name != 'professors' ? $name : 'user';
      $qb = $this->getDoctrine()
                 ->getManager()
                 ->getRepository('FarpostStoreBundle:' . $entities[$name]['c'])
                 ->createQueryBuilder('t');
      $join_field = 'id';
      $sp_field =  $entities[$name]['jf'];
      $join_preffix = 't';
      if ($name == 'auditories' || $name == 'times') {
         $qb->innerJoin('FarpostStoreBundle:Schedule', 's', Join::WITH, 't.id = s.' . $sp_field);
         $join_preffix = 's';
         $sp_field = 'id';
         $join_field = 'schedule_part';
      }
      $qb->innerJoin(
            'FarpostStoreBundle:SchedulePart',
            'sp',
            Join::WITH,
            sprintf('sp.%s = %s.%s', $sp_field, $join_preffix, $join_field)
      );
      $res = $qb->select(array_map(function($a) { return "t.$a"; }, $entities[$name]['f']))
                ->getQuery()
                ->getArrayResult();
      return $response->setStatusCode(200)->setContent(json_encode($res, JSON_UNESCAPED_UNICODE));
   }
}
