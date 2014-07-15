<?php

namespace Farpost\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Farpost\StoreBundle\Entity;
use Doctrine\ORM\Query\Expr\Join;

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
