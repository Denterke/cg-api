<?php
namespace Farpost\StoreBundle\Services;

class EntityDispatcher
{
   private $tables_entities_base;
   private $tables_entities_bu;

   private function check($table_name, $from_bu) {
      return $from_bu
         ?
            array_key_exists($table_name, $this->tables_entities_bu)
         :
            array_key_exists($table_name, $this->tables_entities_base);
   }

   public function __construct() {
      $this->tables_entities_base = [
         'departments'         => 'Department',
         'schools'             => 'School',
         'study_types'         => 'StudyType',
         'schedule'            => 'Schedule',
         'schedule_parts'      => 'SchedulePart',
         'schedule_rendered'   => 'ScheduleRendered',
         'disciplines'         => 'Discipline',
         'discipline_sections' => 'DisciplineSection',
         'roles'               => 'Role',
         'users'               => 'User',
         'groups'              => 'Group',
         'study_sets'          => 'StudySet',
         'courses'             => 'Course',
         'specializations'     => 'Specialization',
         'geoobjects'          => 'GeoObject',
         'times'               => 'Time',
         'lesson_types'        => 'LessonType',
         'geoobject_types'     => 'GeoObjectType',
         'levels'              => 'Level',
         'buildings'           => 'Building',
         'building_types'      => 'BuildingType',
         'versions'            => 'Version',
         'ssources'            => 'ScheduleSource',
         'objects'             => 'GeoObject',
         'object_types'        => 'GeoObjectType'
      ];

      $this->tables_entities_bu = [
         'objects'             => 'Object',
         'buildings'           => 'Building',
         'object_types'        => 'ObjectType',
         'node_types'          => 'NodeType',
         'path_segments'       => 'PathSegment',
         'path_segment_points' => 'PathSegmentPoint'
      ];
   }
   public function tableToEntity($table_name, $from_bu = false, $full = false)
   {
      return $this->check($table_name, $from_bu)
         ?
            (($full
               ?
                  $from_bu
                     ?
                        'FarpostBackUpBundle:'
                     :
                        'FarpostStoreBundle:'
               :
                  ""
            ) .
            ($from_bu
               ?
                  $this->tables_entities_bu[$table_name]
               :
                  $this->tables_entities_base[$table_name]
            ))
         :
            '';
   }
}
