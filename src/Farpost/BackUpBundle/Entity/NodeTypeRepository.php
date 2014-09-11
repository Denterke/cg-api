<?php

namespace Farpost\BackUpBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/*
 * NodeTypeRepository
 */
class NodeTypeRepository extends EntityRepository
{
   private function _prepareQB()
   {
      $qb = $this->_em->createQueryBuilder();
      $qb->select('nt')
         ->distinct()
         ->from('FarpostBackUpBundle:NodeType', 'nt');
      return $qb;
   }

   private function _finalizeRaw(&$recs)
   {
      $result = [];
      foreach($recs as $rec) {
         $elem = [
            'id'     => $rec->getId(),
            'alias'  => $rec->getAlias()
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