<?php

namespace Farpost\BackUpBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/*
 * PathSegmentRepository
 */
class PathSegmentRepository extends EntityRepository
{
   private function _prepareQB()
   {
      $qb = $this->_em->createQueryBuilder();
      $qb->select('ps')
         ->distinct()
         ->from('FarpostBackUpBundle:PathSegment', 'ps');
      return $qb;
   }

   private function _finalizeRaw(&$recs)
   {
      $result = [];
      foreach($recs as $rec) {
         $elem = [
            'id'             => $rec->getId(),
            'level'          => $rec->getLevel(),
            'id_vertex_from' => $rec->getObjectFrom()->getId(),
            'id_vertex_to'   => $rec->getObjectTo()->getId()
         ];
         array_push($result, $elem);
      }
      return $result;
   }

   public function getRawResults()
   {
      $recs = $this->_prepareQB()->getQuery()
                   ->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true)
                   ->getArrayResult();
      return $recs;
   }
}