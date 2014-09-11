<?php

namespace Farpost\BackUpBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/*
 * PathSegmentPointRepository
 */
class PathSegmentPointRepository extends EntityRepository
{
   private function _prepareQB()
   {
      $qb = $this->_em->createQueryBuilder();
      $qb->select('psp')
         ->distinct()
         ->from('FarpostBackUpBundle:PathSegmentPoint', 'psp');
      return $qb;
   }

   private function _finalizeRaw(&$recs)
   {
      $result = [];
      foreach($recs as $rec) {
         $elem = [
            'id'      => $rec->getId(),
            'path_id' => $rec->getPathSegment() ? $rec->getPathSegment()->getId() : "",
            'lat'     => $rec->getLat(),
            'lon'     => $rec->getLon(),
            'idx'     => $rec->getIdx()
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