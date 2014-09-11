<?php

namespace Farpost\StoreBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/*
 * SchoolRepository
 */
class GroupRepository extends EntityRepository
{
   private function _prepareQB()
   {
      $qb = $this->_em->createQueryBuilder();
      $qb->select('g')
         ->distinct()
         ->from('FarpostStoreBundle:Group', 'g');
      return $qb;
   }

   // public function getList()
   // {
      // return $this->_prepareQB()
                  // ->getQuery()
                  // ->getArrayResult();
   // }

   public function syncValue($alias, $study_set)
   {
      $qb = $this->_prepareQB();
      try {
         $group = $qb->where('g.alias = ?1')
                     ->andWhere($qb->expr()->eq('g.study_set', '?2'))
                     ->setParameter(1, $alias)
                     ->setParameter(2, $study_set->getId())
                     ->getQuery()
                     ->getSingleResult();
         $this->_em->createQueryBuilder()
                   ->delete('FarpostStoreBundle:SchedulePart', 'sp')
                   ->where($qb->expr()->eq('sp.group', '?1'))
                   ->setParameter(1, $group->getId())
                   ->getQuery()
                   ->getResult();
      }
      catch (\Doctrine\ORM\NoResultException $e) {
         error_log("group no result");
         $group = new Group();
         $group->setAlias($alias)->setStudySet($study_set);
         $this->_em->persist($group);
         $this->_em->flush();
         return $group;
      }
      return $group;
   }
}