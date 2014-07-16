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
      //SELECT * FROM Groups g INNER JOIN Study_sets ss ON g.study_set_id = ss.id
      //INNER JOIN department_sets ds ON ss.id = ds.study_set_id INNER JOIN
      //Departments d ON ds.department_id = d.id INNER JOIN Study_types st ON
      //st.id = d.study_type_id INNER JOIN Schools s ON s.id = d.school_id WHERE
      //s.id = $s_id AND st.id = $st_id
      $em = $this->getDoctrine()->getEntityManager();
      $repository = $em->getRepository('FarpostStoreBundle:Group');
      $qb = $repository->createQueryBuilder('g');
      $qb->innerJoin('FarpostStoreBundle:StudySet', 'ss', Join::WITH, 'g.study_set = ss.id')
         ->join('ss.departments', 'departments')
         ->innerJoin('FarpostStoreBundle:School', 's', Join::WITH, 'departments.school = s.id')
         ->innerJoin('FarpostStoreBundle:StudyType', 'st', Join::WITH, 'departments.study_type = st.id')
         ->where('st.id = :st_id')
         ->andwhere('s.id = :s_id');
      $qb->setParameters([
         'st_id' => $study_type_id,
         's_id'  => $school_id
      ]);
      echo $qb->getQuery()->getSQL();
      echo $study_type_id;
      echo $school_id;
      $result = $qb->getQuery()->getArrayResult();
      $response = new Response(json_encode($result));
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
      )->where(
         'sp.group = ?1'
      )->setParameter(1, $request->query->getInt('group', 0));
      $res = $qb->select(array_map(function($a) { return "t.$a"; }, array_merge($entities[$name]['f'], ['id'])))
                ->distinct()
                ->getQuery()
                ->getArrayResult();
      if ($name == 'times') {
         $res = array_map(function ($v) {
            $v['end_time'] = $v['end_time']->format('H:i');
            $v['start_time'] = $v['start_time']->format('H:i');
            return $v;
         }, $res);
      }
      return $response->setStatusCode(200)->setContent(json_encode($res, JSON_UNESCAPED_UNICODE));
   }
}
