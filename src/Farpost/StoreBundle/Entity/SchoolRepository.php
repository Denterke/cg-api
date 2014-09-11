<?php

namespace Farpost\StoreBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/*
 * SchoolRepository
 */
class SchoolRepository extends EntityRepository
{
   private function _prepareQB()
   {
      $qb = $this->_em->createQueryBuilder();
      $qb->select('sc.id, sc.alias')
         ->distinct()
         ->from('FarpostStoreBundle:School', 'sc');
      return $qb;
   }

   public function getList()
   {
      return $this->_prepareQB()
                  ->getQuery()
                  ->getArrayResult();
   }

   public function syncValue($alias)
   {
      $school = $this->findOneBy(['alias' => $alias]);
      if (!is_null($school)) {
         return $school;
      }
      $school = new School();
      $school->setAlias($alias);
      $this->_em->persist($school);
      $this->_em->flush();
      return $school;
   }
}