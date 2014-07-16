<?php

namespace Farpost\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Farpost\StoreBundle\Entity;
use Doctrine\ORM\Query\Expr\Join;

class DefaultController extends Controller
{
   function normJsonStr($str){
       $str = preg_replace_callback('/\\\u([a-f0-9]{4})/i', create_function('$m', 'return chr(hexdec($m[1])-1072+224);'), $str);
       return iconv('cp1251', 'utf-8', $str);
       return $str;
   }

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
      if (!in_array($name, ['building', 'school'])) {
         return $response;
      }
      $em = $this->getDoctrine()->getEntityManager();
      $items = $em->getRepository('FarpostStoreBundle:' . ucfirst($name))
               ->createQueryBuilder('g')
               ->getQuery()
               ->getArrayResult();
      return $response->setStatusCode(200)
                      ->setContent(json_encode($items, JSON_UNESCAPED_UNICODE));
   }

   public function getGroupsAction()
   {
      $response = $this->_CreateResponse();
      parse_str($_SERVER['QUERY_STRING']);
      if (!isset($study_type) || !isset($school)) {
         return $response;
      }
      $em = $this->getDoctrine()->getEntityManager();
      $qb = $em->getRepository('FarpostStoreBundle:Group')
               ->createQueryBuilder('g')
               ->innerJoin('FarpostStoreBundle:StudySet', 'ss', Join::WITH, 'g.study_set = ss.id')
               ->join('ss.departments', 'departments')
               ->innerJoin('FarpostStoreBundle:School', 's', Join::WITH, 'departments.school = s.id')
               ->innerJoin('FarpostStoreBundle:StudyType', 'st', Join::WITH, 'departments.study_type = st.id')
               ->where('st.id = :st_id')
               ->andwhere('s.id = :s_id')
               ->setParameters([
                 'st_id' => $study_type,
                 's_id'  => $school
              ]);
      $result = $qb->getQuery()->getArrayResult();
      $response->setContent(json_encode($result, JSON_UNESCAPED_UNICODE))
               ->setStatusCode(200);
      return $response;
   }

   public function getForGroupAction($name)
   {
      $request = Request::createFromGlobals();
      $response = $this->_CreateResponse();
      $entities = [
         'times'       => ['c' => 'Time',       'jf' => 'time',       'f' => ['end_time', 'start_time']],
         //разобраться с doctrine foreign key
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
