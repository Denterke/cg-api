<?php
namespace Farpost\StoreBundle\Classes;

class GroupCache
{
   private $pdo;
   private $schools;         //alias => [id]
   private $studyTypes;      //alias => [id]
   private $specializations; //alias => [id]
   private $courses;         //alias => [id]
   private $departments;     //id => [schoolId, studyTypeId, alias]
   private $studySets;       //id => [specId, courseId, departmentId]
   private $groups;          //alias => [id, studySetId]

   private function append(&$arr, $key, $val) //associative array
   {
      // echo json_encode([
         // 'key' => $key,
         // 'arr' => $arr
      // ]);
      if (array_key_exists($key, $arr)) {
         array_push($arr[$key], $val);
      } else {
         $arr[$key] = [];
         array_push($arr[$key], $val);
      }
   }

   private function PDOInsert($cols, $vals, $table, $returnId = true)
   {
      $cols = implode(', ', $cols);
      $vals = implode(', ', $vals);
      $insStr = "INSERT INTO $table ($cols) VALUES ($vals) " . ($returnId ? "RETURNING id;" : ";");
      $stmt = $this->pdo->prepare($insStr);
      $stmt->execute();
      if ($returnId) {
         return $stmt->fetchAll()[0]['id'];
      }
   }

   private function PDOClearGroupSchedule($gId)
   {
      // $stmt = $this->pdo->prepare("
      //    ALTER TABLE schedule_rendered DISABLE TRIGGER USER;
      // ");
      // $stmt->execute();
      $stmt = $this->pdo->prepare("

         DELETE FROM
            schedule s
         WHERE
            s.id
         IN
            (SELECT
               s2.id
             FROM
               schedule s2
             INNER JOIN 
               schedule_parts sp
             ON
               s2.schedule_part_id = sp.id
             WHERE
               sp.group_id = :groupId);"
      );
      $stmt->bindValue(':groupId', $gId, \PDO::PARAM_INT);
      $stmt->execute();
      // $stmt = $this->pdo->prepare("
      //    ALTER TABLE schedule_rendered ENABLE TRIGGER USER;
      // ");
      // $stmt->execute();
   }

   public function statistics()
   {
      return "schools:         " . count($this->schools)         . "\n" .
             "study types:     " . count($this->studyTypes)      . "\n" . 
             "specializations: " . count($this->specializations) . "\n" .
             "courses:         " . count($this->courses)         . "\n" .
             "departments:     " . count($this->departments)     . "\n" .
             "study sets:      " . count($this->studySets)       . "\n" .
             "groups:          " . count($this->groups)          . "\n"; 
   }

   public function __construct($pdo)
   {
      $this->pdo = $pdo;
      $this->schools = [];
      $this->studyType = [];
      $this->specializations = [];
      $this->courses = [];
      $this->departments = [];
      $this->studySets = [];
      $this->groups = [];



      $stmt = $this->pdo->prepare("SELECT id, alias FROM schools");
      $stmt->execute();
      while($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
         $this->schools[$row['alias']] = $row['id'];
      }

      $stmt = $this->pdo->prepare("SELECT id, alias FROM study_types");
      $stmt->execute();
      while($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
         $this->studyTypes[$row['alias']] = $row['id'];
      }

      $stmt = $this->pdo->prepare("SELECT id, alias FROM specializations");
      $stmt->execute();
      while($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
         $this->specializations[$row['alias']] = $row['id'];
      }

      $stmt = $this->pdo->prepare("SELECT id, alias FROM courses");
      $stmt->execute();
      while($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
         $this->courses[$row['alias']] = $row['id'];
      }

      $stmt = $this->pdo->prepare("SELECT id, alias, study_type_id, school_id FROM departments");
      $stmt->execute();
      while($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
         $this->departments[$row['id']] = [
            'alias'       => $row['alias'],
            'studyTypeId' => $row['study_type_id'],
            'schoolId'    => $row['school_id']
         ];
      }      

      $stmt = $this->pdo->prepare("SELECT id, specialization_id, course_id, department_id FROM study_sets");
      $stmt->execute();
      while($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
         $this->studySets[$row['id']] = [
            'specId'       => $row['specialization_id'],
            'courseId'     => $row['course_id'],
            'depId' => $row['department_id']
         ];
      }

      $stmt = $this->pdo->prepare("SELECT id, alias, study_set_id FROM groups");
      $stmt->execute();
      while($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
         $this->groups[$row['alias']] = [
            'id'         => $row['id'],
            'studySetId' => $row['study_set_id']
         ];
      }   
   }

   public function syncGroupInfo($school, $st, $spec, $course, $department, $group)
   {
      if (!isset($this->schools[$school])) {
         $this->schools[$school] = $this->PDOinsert(
            ['alias'],
            ["'$school'"],
            'schools'
         );
      }
      $schoolId = $this->schools[$school];

      
      if (!isset($this->studyTypes[$st])) {
         $this->studyTypes[$st] = $this->PDOInsert(
            ['alias'],
            ["'$st'"],
            'study_types'
         );
      }
      $stId = $this->studyTypes[$st];

      if (!isset($this->specializations[$spec])) {
         $this->specializations[$spec] = $this->PDOInsert(
            ['alias'],
            ["'$spec'"],
            'specializations'
         );
      }
      $specId = $this->specializations[$spec];

      if (!isset($this->courses[$course])) {
         $this->courses[$course] = $this->PDOInsert(
            ['alias'],
            ["'$course'"],
            'courses'
         );
      }
      $courseId = $this->courses[$course];

      $depId = null;
      foreach($this->departments as $key => &$dep) {
         if ($dep['alias'] == $department &&
             $dep['schoolId'] == $schoolId &&
             $dep['studyTypeId'] == $stId) {
            $depId = $key;
            break;
         }
      }
      if (!isset($depId)) {
         $depId = $this->PDOInsert(
            ['school_id', 'study_type_id', 'alias'],
            [$schoolId, $stId, "'$department'"],
            'departments'
         );
         $this->departments[$depId] = [
            'alias'       => $department,
            'schoolId'    => $schoolId,
            'studyTypeId' => $stId
         ];
      }

      $ssId = null;
      foreach($this->studySets as $id => &$ss) {
         if ($ss['specId'] == $specId &&
             $ss['courseId'] == $courseId &&
             $ss['depId'] == $depId) {
            $ssId = $id;
            break;
         }
      }
      if (!isset($ssId)) {
         $ssId = $this->PDOInsert(
            ['specialization_id', 'course_id', 'department_id'],
            [$specId, $courseId, $depId],
            'study_sets'
         );
         $this->studySets[$ssId] = [
            'specId'   => $specId,
            'courseId' => $courseId,
            'depId'    => $depId
         ];
      }
      if (!isset($this->groups[$group])) {
         $this->groups[$group] = [
            'id'         =>  $this->PDOInsert(
                                ['alias', 'study_set_id'],
                                ["'$group'", $ssId],
                                'groups'
                             ),
            'studySetId' => $ssId
         ];
         return $this->groups[$group]['id'];
      }
      $gId = $this->groups[$group]['id'];
      $this->PDOClearGroupSchedule($gId);
      return $gId;
   }
}