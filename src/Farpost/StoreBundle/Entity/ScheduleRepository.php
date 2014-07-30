<?php

namespace Farpost\StoreBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/*
 *ScheduleRepository
 */
class ScheduleRepository extends EntityRepository
{
   private function _prepareQB()
   {
      $qb = $this->_em->createQueryBuilder();
      $qb->select('s')
         ->distinct()
         ->from('FarpostStoreBundle:Schedule', 's')
         ->innerJoin('FarpostStoreBundle:SchedulePart', 'sp', Join::WITH, 's.schedule_part = sp.id')
         ->innerJoin('FarpostStoreBundle:Time',         't',  Join::WITH, 's.time = t.id')
         ->innerJoin('FarpostStoreBundle:Group',        'g',  Join::WITH, 'sp.group = g.id');
      return $qb;
   }

   private function _finalize(&$recs)
   {
      $result = [];
      foreach ($recs as &$elem) {
         $schedule_elem = [
            "Группа"         => $elem->getSchedulePart()->getGroup()     ->getAlias(),
            "Тип занятия"    => $elem->getLessonType()  ->getAlias(),
            "Дисциплина"     => $elem->getSchedulePart()->getDiscipline()->getAlias(),
            "Пара"           => $elem->getTime()        ->getAlias(),
            "Аудитория"      => $elem->getAuditory()    ->getAlias(),
            "Профессор"      => $elem->getSchedulePart()->getProfessor() ->getId(),
            "id"             => $elem->getId()
         ];
         array_push($result, $schedule_elem);
      }
      return $result;
   }

   public function getForGroup($group_id)
   {
      $recs = $this->_prepareQB()
                   ->where('g.id = :group_id')
                   ->andWhere('s.time_start <= CURRENT_DATE()')
                   ->andWhere('s.time_end >= CURRENT_DATE()')
                   ->orderBy('s.time_start', 'ASC')
                   ->orderBy('t.start_time', 'ASC')
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
                  ->andWhere('s.time_start <= CURRENT_DATE()')
                  ->andWhere('s.time_end >= CURRENT_DATE()')
                  ->setParameter('table_name', 'schedule_rendered')
                  ->setParameter('time', $last_time)
                  ->setParameter('group_id', $group_id)
                  ->getQuery()
                  ->getResult();
      $recs = $this->_finalizeUpdate($recs);
      return $recs;
   }
}