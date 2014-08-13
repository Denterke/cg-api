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

   private function _createResponse()
   {
      return new Response('Not found', 404, ['Content-Type' => 'application/json']);
   }

   private function _tableToEntity($table_name)
   {
      $tables_entities = [
         'departments' => 'Department',
         'schools' => 'School',
         'study_types' => 'StudyType',
         'schedule' => 'Schedule',
         'schedule_parts' => 'SchedulePart',
         'schedule_rendered' => 'ScheduleRendered',
         'disciplines' => 'Discipline',
         'discipline_sections' => 'DisciplineSection',
         'roles' => 'Role',
         'users' => 'User',
         'groups' => 'Group',
         'study_sets' => 'StudySet',
         'courses' => 'Course',
         'specializations' => 'Specialization',
         'auditories' => 'Auditory',
         'times' => 'Time',
         'lesson_types' => 'LessonType',
         'auditory_types' => 'AuditoryType',
         'levels' => 'Level',
         'buildings' => 'Building',
         'building_types' => 'BuildingType'
      ];
      return $tables_entities[$table_name];
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
               ->createQueryBuilder('g')
               ->getQuery()
               ->getArrayResult();
      return $response->setStatusCode(200)
                      ->setContent(json_encode(
                        [$name . 's' => $items]
                        // JSON_UNESCAPED_UNICODE
                        ));
   }

   public function getGroupsAction()
   {
      $response = $this->_createResponse();
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
      $response->setContent(json_encode(
                              ['groups' => $result]
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
         'auditories'  => 'Auditory',
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
         'Auditory' => 'auditories',
         'User' => 'professors',
         'Time' => 'times',
         'Discipline' => 'disciplines',
         'ScheduleRendered' => 'schedules'
      ];
      foreach($entities as $en_name => $table_name) {
         $elem = $this->getDoctrine()->getManager()
                      ->getRepository('FarpostStoreBundle:' . $en_name)
                      ->getUpdate($dt, $group);
         if (empty($elem)) continue;
         array_push(
            $result,
            [$table_name => $elem]
         );
      }
      $current_time = new \DateTime();
      array_push($result, ['timestamp' => $current_time->getTimestamp()]);
      $result = json_encode($result);
      $result = substr($result, 1, strlen($result) - 2);
      return $response->setStatusCode(200)->setContent($result);
   }

   public function getBaseUpdatesAction()
   {
      
   }

   public function getFileAction($filename)
   {
      echo 1;
      $response = new Response('Not found', 404);
      if (!ctype_alnum($filename) || !preg_match('/^(?:[a-z0-9_-]|\.(?!\.))+$/iD', $filename)) {
         return $response;
      }
      $filepath = $this->get('kernel')->getRootDir() . 'web/static/' . $filename;
      if (!file_exists($filepath)) {
         return $response;
      }
      $response = new Response();
      $response->headers->set('Cache-Control', 'private');
      $response->headers->set('Content-Type', mime_content_type($filepath));
      $response->headers->set('Content-Disposition', 'attachment; filename="' . $filepath . '";');
      $response->headers->set('Content-length', filesize($filepath));
      $response->sendHeaders();
      $response->setContent(file_get_contents($filename));
      return $response;
   }
}
