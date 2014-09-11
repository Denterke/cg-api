<?php

namespace Farpost\StoreBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/*
 *DisciplineRepository
 */
class DisciplineRepository extends EntityRepository
{
   private function _prepareQB()
   {
      $qb = $this->_em->createQueryBuilder();
      $qb->select('d.id, d.alias')
         ->distinct()
         ->from('FarpostStoreBundle:Discipline', 'd')
         ->innerJoin('FarpostStoreBundle:SchedulePart', 'sp', Join::WITH, 'd.id = sp.discipline')
         ->innerJoin('FarpostStoreBundle:Group',        'g',  Join::WITH, 'sp.group = g.id');
      return $qb;
   }

   public function getForGroup($group_id)
   {
      $qb = $this->_prepareQB();
      return $qb->where('g.id = :group_id')
                 ->setParameter('group_id', $group_id)
                 ->getQuery()
                 ->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true)
                 ->getArrayResult();
   }

   public function getUpdate($last_time, $group_id)
   {
      return $this->_prepareQB()
                  ->select('d.id, d.alias, lm.status')
                  ->innerJoin('FarpostStoreBundle:LastModified', 'lm', Join::WITH, 'lm.record_id = d.id')
                  ->where('lm.table_name = :table_name')
                  ->andWhere('lm.last_modified > :time')
                  ->andWhere('g.id = :group_id')
                  ->setParameter('table_name', 'disciplines')
                  ->setParameter('time', $last_time)
                  ->setParameter('group_id', $group_id)
                  ->getQuery()
                  ->getArrayResult();
   }

   public function syncValue($alias)
   {
      $discipline = $this->findOneBy(['alias' => $alias]);
      if (!is_null($discipline)) {
         return $discipline;
      }
      $discipline = new Discipline();
      $discipline->setAlias($alias);
      $this->_em->persist($discipline);
      $this->_em->flush();
      return $discipline;
   }
}