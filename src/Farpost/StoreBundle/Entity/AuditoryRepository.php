<?php

namespace Farpost\StoreBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/*
 *AuditoryRepository
 */
class AuditoryRepository extends EntityRepository
{
   private function _prepareQB()
   {
      $qb = $this->_em->createQueryBuilder();
      $qb->select('a')
         ->distinct()
         ->from('FarpostStoreBundle:Auditory', 'a')
         ->innerJoin('FarpostStoreBundle:Schedule',     's',  Join::WITH, 'a.id = s.auditory')
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
      return $recs;
   }

   private function _finalize(&$recs)
   {
      $recs = array_map(function ($v) {
         $type_id = $v['auditory_type_id'];
         unset($v['auditory_type_id']);
         $v['type_id'] = $type_id;
         return $v;
      }, $recs);
      return $recs;
   }

   public function getForGroup($group_id)
   {
      $recs = $this->_prepareQB()
                   ->where('g.id = :group_id')
                   ->setParameter('group_id', $group_id)
                   ->getQuery()
                   ->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true)
                   ->getArrayResult();
      return $this->_finalize($recs);
   }

   public function getUpdate($last_time, $group_id)
   {
      $recs = $this->_prepareQB()
                  ->select('a, lm.status')
                  ->innerJoin('FarpostStoreBundle:LastModified', 'lm', Join::WITH, 'lm.record_id = a.id')
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
}