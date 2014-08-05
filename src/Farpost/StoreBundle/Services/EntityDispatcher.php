<?php
namespace Farpost\StoreBundle\Services;

class EntityDispatcher
{
   public function tableToEntity($table_name)
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
}
