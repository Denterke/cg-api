<?php

namespace Farpost\BackUpBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/*
 * ObjectRepository
 */
class ObjectRepository extends EntityRepository
{
   private function _prepareQB()
   {
      $qb = $this->_em->createQueryBuilder();
      $qb->select('o')
         ->distinct()
         ->from('FarpostBackUpBundle:Object', 'o');
      return $qb;
   }

   private function _finalizeRaw(&$recs)
   {
      $result = [];
      foreach($recs as $rec) {
         $elem = [
            'id' => $rec->getId(),
            'type_id' => $rec->getObjectType() ? $rec->getObjectType()->getId() : "",
            'building_id' => $rec->getBuilding() ? $rec->getBuilding()->getId() : "",
            'level'       => $rec->getLevel(),
            'alias'       => $rec->getAlias(),
            'node_id'     => $rec->getNodeType() ? $rec->getNodeType()->getId() : "",
            'lat'         => $rec->getLat(),
            'lon'         => $rec->getLon(),
            'status'      => $rec->getStatus()
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