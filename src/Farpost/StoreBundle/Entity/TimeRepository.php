<?php

namespace Farpost\StoreBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/*
 *TimeRepository
 */
class TimeRepository extends EntityRepository
{
   private function _prepareQB()
   {
      $qb = $this->_em->createQueryBuilder();
      $qb->select('t')
         ->distinct()
         ->from('FarpostStoreBundle:Time', 't')
         ->innerJoin('FarpostStoreBundle:Schedule',     's',  Join::WITH, 't.id = s.time')
         ->innerJoin('FarpostStoreBundle:SchedulePart', 'sp', Join::WITH, 's.schedule_part = sp.id')
         ->innerJoin('FarpostStoreBundle:Group',        'g',  Join::WITH, 'sp.group = g.id');
      return $qb;
   }

   private function _finalizeUpdate(&$recs)
   {
      $recs = array_map(function ($v) {
         $elem = $v['0'];
         $elem['status'] = $v['status'];
         return $elem;
      }, $recs);
   }

   private function _finalize(&$recs)
   {
      $recs = array_map(function ($v) {
         $v['end_time'] = $v['end_time']->format('H:i');
         $v['start_time'] = $v['start_time']->format('H:i');
         return $v;
      }, $recs);
      return $recs;
   }

   public function getForGroup($group_id)
   {
      $qb = $this->_prepareQB();
      $recs = $qb->where('g.id = :group_id')
                 ->setParameter('group_id', $group_id)
                 ->getQuery()
                 ->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true)
                 ->getArrayResult();
      $this->_finalize($recs);
      return $recs;
   }

   public function getUpdate($last_time, $group_id)
   {
      $recs =  $this->_prepareQB()
                  ->select('t, lm.status')
                  ->innerJoin('FarpostStoreBundle:LastModified', 'lm', Join::WITH, 'lm.record_id = t.id')
                  ->where('lm.table_name = :table_name')
                  ->andWhere('lm.last_modified > :time')
                  ->andWhere('g.id = :group_id')
                  ->setParameter('table_name', 'auditories')
                  ->setParameter('time', $last_time)
                  ->setParameter('group_id', $group_id)
                  ->getQuery()
                  ->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true)
                  ->getArrayResult();
      $this->_finalizeUpdate($recs);
      $this->_finalize($recs);
      return $recs;
   }

   public function syncValue($alias)
   {
      $time = $this->findOneBy(['alias' => $alias]);
      if (is_null($time)) {
         throw new \Exception('No such time available!');
      }
      return $time;
   }
}