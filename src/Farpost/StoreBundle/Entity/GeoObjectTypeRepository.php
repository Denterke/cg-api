<?php

namespace Farpost\StoreBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/*
 * PathSegmentRepository
 */
class GeoObjectTypeRepository extends EntityRepository
{
   private function _prepareQB()
   {
      $qb = $this->_em->createQueryBuilder();
      $qb->select('ot')
         ->distinct()
         ->from('FarpostStoreBundle:GeoObjectType', 'ot');
      return $qb;
   }

//    private function _finalizeRaw(&$recs)
//    {
//       $result = [];
//       foreach($recs as $rec) {
//          $elem = [
//             'id'             => $rec->getId(),
//             'level'          => $rec->getLevel(),
//             'id_vertex_from' => $rec->getObjectFrom()->getId(),
//             'id_vertex_to'   => $rec->getObjectTo()->getId()
//          ];
//          array_push($result, $elem);
//       }
//       return $result;
//    }

//    public function getRawResults()
//    {
//       $recs = $this->_prepareQB()->getQuery()
//                    ->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true)
//                    ->getArrayResult();
//       return $recs;
//    }
   public function synchronizeWith($items)
   {
      $qb = $this->_prepareQB();
      $batch_size = 100;
      $i = 0;
      $em = $this->_em;
      foreach ($items as &$item) {
         $is_new = false;
         $i++;
         try {
            $twin = $qb->where('ot.id = :id')
                    ->setParameter('id', $item['id'])
                    ->getQuery()->getSingleResult();
         }
         catch (\Doctrine\ORM\NoResultException $e) {
            $twin = new GeoObjectType();
            $twin->setId($item['id']);
            $is_new = true;
         }
         $twin->setAlias($item['alias'])
              ->setDisplayed($item['display']);
         if ($is_new) {
            $em->persist($twin);
         } else {
            $em->merge($twin);
         }
         if (($i % $batch_size) === 0) {
            $em->flush();
            $em->clear();
         }
      }
      $em->flush();
      $em->clear();
   }

}