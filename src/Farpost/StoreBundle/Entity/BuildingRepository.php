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
      $qb->select('b')
         ->distinct()
         ->from('FarpostStoreBundle:Building', 'b');
      return $qb;
   }

   private function _finalizeList(&$recs)
   {
      $result = [];
      foreach ($recs as $rec) {
         $elem = [
            'id'     => $rec->getId(),
            'number' => $rec->getNumber(),
            'alias'  => $rec->getAlias()
         ];
         array_push($result, $elem);
      }
      return $result;
   }

   public function getList()
   {
      $recs = $this->_prepareQB()
                   ->getQuery()
                   ->getResult();
      return $this->_finalizeList($recs);
   }

   public function synchronizeWith($items)
   {
      $qb = $this->_prepareQB();
      $em = $this->_em;
      $batch_size = 100;
      $i = 0;
      foreach ($items as &$item) {
         $i++;
         $is_new = false;
         try {
            $twin = $qb->where('b.id = :id')
                       ->setParameter('id', $item['id'])
                       ->getQuery()->getSingleResult();
         }
         catch (\Doctrine\ORM\NoResultException $e) {
            $twin = new Building();
            $twin->setId($item['id']);
            $is_new = true;
         }
         $twin->setAlias($item['alias'])
              ->setNumber($item['number'])
              ->setLon($item['lon'])
              ->setLat($item['lat']);
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