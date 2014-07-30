<?php

namespace Farpost\StoreBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/*
 *ScheduleRenderedRepository
 */
class ScheduleRenderedRepository extends EntityRepository
{
   private function _prepareQB()
   {
      $qb = $this->_em->createQueryBuilder();
      $qb->select('sr')
         ->distinct()
         ->from('FarpostStoreBundle:ScheduleRendered', 'sr')
         ->innerJoin('FarpostStoreBundle:Schedule',     's',  Join::WITH, 'sr.schedule = s.id')
         ->innerJoin('FarpostStoreBundle:SchedulePart', 'sp', Join::WITH, 's.schedule_part = sp.id')
         ->innerJoin('FarpostStoreBundle:Group',        'g',  Join::WITH, 'sp.group = g.id')
         ->innerJoin('FarpostStoreBundle:Semester',     'sm', Join::WITH, 's.semester = sm.id');
      return $qb;
   }

   private function _finalizeUpdate(&$recs)
   {
      $result = [];
      foreach ($recs as &$elem) {
         $schedule_template = $elem['0']->getSchedule();
         $schedule_elem = [
            "group_id"       => $schedule_template->getSchedulePart()->getGroup()->getId(),
            "lesson_type_id" => $schedule_template->getLessonType()->getId(),
            "discipline_id"  => $schedule_template->getSchedulePart()->getDiscipline()->getId(),
            "time_id"        => $schedule_template->getTime()->getId(),
            "auditory_id"    => $schedule_template->getAuditory()->getId(),
            "professor_id"   => $schedule_template->getSchedulePart()->getProfessor()->getId(),
            "date"           => $elem['0']->getExecDate()->getTimestamp(),
            "id"             => $elem['0']->getId(),
            "status"         => $elem['status']
         ];
         array_push($result, $schedule_elem);
      }
      return $result;
   }

   private function _finalize(&$recs)
   {
      $result = [];
      foreach ($recs as &$elem) {
         $schedule_template = $elem->getSchedule();
         $schedule_elem = [
            "group_id"       => $schedule_template->getSchedulePart()->getGroup()->getId(),
            "lesson_type_id" => $schedule_template->getLessonType()->getId(),
            "discipline_id"  => $schedule_template->getSchedulePart()->getDiscipline()->getId(),
            "time_id"        => $schedule_template->getTime()->getId(),
            "auditory_id"    => $schedule_template->getAuditory()->getId(),
            "professor_id"   => $schedule_template->getSchedulePart()->getProfessor()->getId(),
            "date"           => $elem->getExecDate()->getTimestamp(),
            "id"             => $elem->getId(),
            "status"         => 0
         ];
         array_push($result, $schedule_elem);
      }
      return $result;
   }

   public function getForGroup($group_id)
   {
      $recs = $this->_prepareQB()
                   ->where('g.id = :group_id')
                   ->andWhere('sm.id = 1')
                   ->setParameter('group_id', $group_id)
                   ->getQuery()
                   ->getResult();
      $result =  $this->_finalize($recs);
      return $result;
   }

   public function getUpdate($last_time, $group_id)
   {
      $recs = $this->_prepareQB()
                  ->select('sr, lm.status')
                  ->innerJoin('FarpostStoreBundle:LastModified', 'lm', Join::WITH, 'lm.record_id = s.id')
                  ->where('lm.table_name = :table_name')
                  ->andWhere('lm.last_modified > :time')
                  ->andWhere('g.id = :group_id')
                  ->andWhere('sm.id = 1')
                  ->setParameter('table_name', 'schedule_rendered')
                  ->setParameter('time', $last_time)
                  ->setParameter('group_id', $group_id)
                  ->getQuery()
                  ->getResult();
      $recs = $this->_finalizeUpdate($recs);
      return $recs;
   }
}