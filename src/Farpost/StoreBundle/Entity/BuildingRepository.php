<?php

namespace Farpost\StoreBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/*
 * BuildingRepository
 */
class BuildingRepository extends EntityRepository
{
   private function _prepareQB()
   {
      $qb = $this->_em->createQueryBuilder();
      $qb->select('b.id, b.number, b.alias')
         ->distinct()
         ->from('FarpostStoreBundle:Building', 'b');
      return $qb;
   }

   public function getList()
   {
      return $this->_prepareQB()
                  ->getQuery()
                  ->getArrayResult();
   }
}