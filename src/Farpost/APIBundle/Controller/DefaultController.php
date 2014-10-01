<?php

namespace Farpost\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Farpost\StoreBundle\Entity;
use Farpost\StoreBundle\Entity\Time;
use Farpost\StoreBundle\Entity\Semester;
use Doctrine\ORM\Query\Expr\Join;

class DefaultController extends Controller
{
   function normJsonStr($str){
      $str = preg_replace_callback('/\\\u([a-f0-9]{4})/i', create_function('$m', 'return chr(hexdec($m[1])-1072+224);'), $str);
      return iconv('cp1251', 'utf-8', $str);
      return $str;
   }

   private function _createResponse()
   {
      return new Response('Not found', 404, ['Content-Type' => 'application/json']);
   }

   public function getTimeAction()
   {
      $dt = new \DateTime();
      $response = $this->_createResponse();
      $response->setStatusCode(200)->setContent(json_encode(['timestamp' => $dt->getTimestamp()]));
      return $response;
   }

   public function loginAction()
   {
      $response = $this->_createResponse();
      return $response->setStatusCode(200)
                      ->setContent(json_encode([
                           'secret'            => 'fuck',
                           'first_name'        => 'Василий',
                           'last_name'         => 'Васильев',
                           'middle_name'       => 'Васильевич',
                           'role_id'           => 1,
                           'group_id'          => 1,
                           'study_type_id'     => 1,
                           'school_id'         => 1,
                           'specialization_id' => 1
      ]));
   }

   public function indexAction($name)
   {
      $response = new Response(json_encode(['method' => $index]));
      $response->headers->set('Content-Type', 'application/json');
      return $response;
   }

   public function listAction($name)
   {
      $response = $this->_createResponse();
      if (!in_array($name, ['building', 'school'])) {
         return $response;
      }
      $em = $this->getDoctrine()->getEntityManager();
      $items = $em->getRepository('FarpostStoreBundle:' . ucfirst($name))
               ->getList();
      return $response->setStatusCode(200)
                      ->setContent(json_encode(
                        [$name . 's' => $items]
                        // JSON_UNESCAPED_UNICODE
                        ));
   }

   public function getGroupsAction()
   {
      $request = Request::createFromGlobals();
      $response = $this->_createResponse();
      // parse_str($_SERVER['QUERY_STRING']);
      // if (!isset($study_type) || !isset($school)) {
      //    return $response;
      // }
      $em = $this->getDoctrine()->getEntityManager();
      $t = $request->query->has('t') ? $request->query->get('t') : 1;
      $result = $em->getRepository('FarpostStoreBundle:Group')
               ->getList($t);
               // ->createQueryBuilder('g');
              //  ->innerJoin('FarpostStoreBundle:StudySet', 'ss', Join::WITH, 'g.study_set = ss.id')
              //  ->join('ss.departments', 'departments')
              //  ->innerJoin('FarpostStoreBundle:School', 's', Join::WITH, 'departments.school = s.id')
              //  ->innerJoin('FarpostStoreBundle:StudyType', 'st', Join::WITH, 'departments.study_type = st.id')
              //  ->where('st.id = :st_id')
              //  ->andwhere('s.id = :s_id')
              //  ->setParameters([
              //    'st_id' => $study_type,
              //    's_id'  => $school
              // ]);
      // $result = $qb->getQuery()->getArrayResult();
      $dt = new \Datetime();
      $response->setContent(json_encode(
                              [
                                 'groups' => $result,
                                 'timestamp' => $dt->getTimestamp()
                              ]
                              // JSON_UNESCAPED_UNICODE
                           ))
               ->setStatusCode(200);
      return $response;
   }

   public function getForGroupAction($name)
   {
      $request = Request::createFromGlobals();
      $response = $this->_createResponse();
      $entities = [
         'times'       => 'Time',
         'auditories'  => 'GeoObject',
         'professors'  => 'User',
         'disciplines' => 'Discipline'
      ];
      if (!$request->query->has('group') || empty($entities[$name])) return $response;
      $en_name = $entities[$name];
      $result = $this->getDoctrine()->getManager()->getRepository('FarpostStoreBundle:' . $en_name)
                     ->getForGroup($request->query->getInt('group', 0));
      return $response->setStatusCode(200)->setContent(json_encode([$name => $result]));
   }

   public function getTypesAction($name)
   {
      $response = $this->_createResponse();
      if (!in_array($name, ['lesson', 'study', 'building']))
         return $response;
      $items = $this->getDoctrine()
                    ->getEntityManager()
                    ->getRepository('FarpostStoreBundle:' . ucfirst($name) . 'Type')
                    ->createQueryBuilder('a')
                    ->getQuery()
                    ->getArrayResult();
      return $response->setStatusCode(200)->setContent(json_encode(
         [$name . '_types' => $items]
         // JSON_UNESCAPED_UNICODE
      ));
   }

   public function getUpdatesAction()
   {
      $request = Request::createFromGlobals();
      $response = $this->_createResponse();
      $dt = new \DateTime();
      if (!$request->query->has('group') || !$request->query->has('t')) return $response;
      $dt->setTimestamp($request->query->getInt('t', 0));
      $group = $request->query->getInt('group', 0);
      $result = [];
      $entities = [
         'GeoObject' => 'auditories',
         'User' => 'professors',
         'Time' => 'times',
         'Discipline' => 'disciplines',
         'ScheduleRendered' => 'schedules'
         // 'Building' => 'buildings'
      ];
      foreach($entities as $en_name => $table_name) {
         $elem = $this->getDoctrine()->getManager()
                      ->getRepository('FarpostStoreBundle:' . $en_name)
                      ->getUpdate($dt, $group);
         if (empty($elem)) continue;
         // array_push(
            $result[$table_name] = $elem;
            // [$table_name => $elem]
         // );
      }
      $current_time = new \DateTime();
      $result['timestamp'] = $current_time->getTimestamp();
      $result = json_encode($result);
      // $result = substr($result, 1, strlen($result) - 2);
      return $response->setStatusCode(200)->setContent($result);
   }

   public function getBaseUpdatesAction()
   {
      $response = $this->_createResponse();
      $result = [
        'update' =>  $this->getDoctrine()->getManager()
                     ->getRepository('FarpostStoreBundle:Version')
                     ->getBases($this->getRequest()->getHost())
         ];
      return $response->setStatusCode(200)->setContent(json_encode($result));
   }

   public function getFileAction($filename)
   {
      $response = new Response('Not found', 404);
      // if (!ctype_alnum($filename)) {
         // return $response;
      // }
      $filepath = $this->getRequest()->server->get('DOCUMENT_ROOT') . '/static/' . $filename;
      if (!file_exists($filepath)) {
         return $response;
      }
      // return $response;
      $response = new Response();
      $response->headers->set('Cache-Control', 'private');
      $response->headers->set('Content-Type', mime_content_type($filepath));
      $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '";');
      $response->headers->set('Content-length', filesize($filepath));
      $response->sendHeaders();
      $response->setContent(file_get_contents($filepath));
      return $response;
   }

   public function getDepartmentsAction(Request $request)
   {
      $response = $this->_createResponse();
      if (!$request->query->has('school') || (!$request->get('school'))) return $response;
      $result = $this->getDoctrine()
           ->getRepository('FarpostStoreBundle:Department')
           ->createQueryBuilder('d')
           ->where('d.school = :school')
           ->setParameters(['school' => $request->get('school')])
           ->getQuery()
           ->getArrayResult();
      if (!empty($result)) {
         $response->setStatusCode(200)->setContent(json_encode($result, JSON_UNESCAPED_UNICODE));
      }
      return $response;
   }

   public function initAppAction()
   {
      $times = [
         [
            "alias" => "1 пара",
            "start_time" => "08:30:00",
            "end_time"   => "10:00:00"
         ],
         [
            "alias" => "2 пара",
            "start_time" => "10:10:00",
            "end_time"   => "11:40:00"
         ],
         [
            "alias" => "3 пара",
            "start_time" => "11:50:00",
            "end_time"   => "13:20:00"
         ],
         [
            "alias" => "4 пара",
            "start_time" => "13:30:00",
            "end_time"   => "15:00:00"
         ],
         [
            "alias" => "5 пара",
            "start_time" => "15:10:00",
            "end_time"   => "16:40:00"
         ],
         [
            "alias" => "6 пара",
            "start_time" => "16:50:00",
            "end_time"   => "18:20:00",
         ],
         [
            "alias" => "7 пара",
            "start_time" => "18:30:00",
            "end_time"   => "20:00:00"
         ],
         [
            "alias" => "8 пара",
            "start_time" => "20:10:00",
            "end_time"   => "21:40:00"
         ]
      ];
      $em = $this->getDoctrine()->getManager('default');
      foreach($times as &$time_t) {
         $time = $em->getRepository('FarpostStoreBundle:Time')
                    ->findOneBy(['alias' => $time_t['alias']]);
         if (is_null($time)) {
            $time = new Time();
            $start_time = new \DateTime();
            $start_time->setTimestamp(strtotime($time_t['start_time']));
            $end_time = new \DateTime();
            $end_time->setTimestamp(strtotime($time_t['end_time']));

            $time->setAlias($time_t['alias'])
                 ->setStartTime($start_time)
                 ->setEndTime($end_time);
            $em->persist($time);
            $em->flush();
         }
      }
      $semester = $em->getRepository('FarpostStoreBundle:Semester')
                     ->findOneBy(['id' => 1]);
      if (is_null($semester)) {
         $semester = new Semester();
         $start_time = new \DateTime();
         $start_time->setTimestamp(strtotime('01.09.2014'));
         $end_time = new \DateTime();
         $end_time->setTimestamp(strtotime('01.01.2015'));
         $semester->setTimeStart($start_time)->setTimeEnd($end_time)->setAlias('Test semester');
         $em->persist($semester);
         $em->flush();
      }
      return $this->_createResponse()->setStatusCode(200)->setContent("all right");
   }
}
