<?php

namespace Farpost\BackUpBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/*
 * ObjectTypeRepository
 */
class ObjectTypeRepository extends EntityRepository
{
   private function _prepareQB()
   {
      $qb = $this->_em->createQueryBuilder();
      $qb->select('ot')
         ->distinct()
         ->from('FarpostBackUpBundle:ObjectType', 'ot');
      return $qb;
   }

   private function _finalizeRaw(&$recs)
   {
      $result = [];
      foreach($recs as $rec) {
         $elem = [
            'id'     => $rec->getId(),
            'alias'  => $rec->getAlias(),
            'display' => $rec->getDisplayed()
         ];
         array_push($result, $elem);
      }
      return $result;
   }

   public function getRawResults()
   {
      $recs = $this->_prepareQB()->getQuery()->getResult();
      return $this->_finalizeRaw($recs);

   }
}