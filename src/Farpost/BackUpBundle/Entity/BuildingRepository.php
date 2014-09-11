<?php

namespace Farpost\BackUpBundle\Entity;
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
      $qb->select('b')
         ->distinct()
         ->from('FarpostBackUpBundle:Building', 'b');
      return $qb;
   }

   private function _finalizeRaw(&$recs)
   {
      $result = [];
      foreach($recs as $rec) {
         // echo $rec;
         $elem = [
            'id'     => $rec->getId(),
            'number' => $rec->getNumber(),
            'alias'  => $rec->getAlias(),
            'lon'    => $rec->getLon(),
            'lat'    => $rec->getLat()
         ];
         array_push($result, $elem);
      }
      return $result;
   }

   // public function getList()
   // {
   //    return $this->_prepareQB()
   //                ->getQuery()
   //                ->getArrayResult();
   // }
   public function getRawResults()
   {
      echo "im fcking here";
      $recs = $this->_prepareQB()->getQuery()->getResult();
      return $this->_finalizeRaw($recs);

   }
}