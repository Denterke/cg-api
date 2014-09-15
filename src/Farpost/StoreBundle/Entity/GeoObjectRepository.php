<?php

namespace Farpost\StoreBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/*
 * GeoObjectRepository
 */
class GeoObjectRepository extends EntityRepository
{
   const AUDITORY_TYPE_ID = 6;
   private function _prepareQB()
   {
      $qb = $this->_em->createQueryBuilder();
      $qb->select('go')
         ->distinct()
         ->from('FarpostStoreBundle:GeoObject', 'go')
         ->innerJoin('FarpostStoreBundle:Schedule',     's',  Join::WITH, 'go.id = s.auditory')
         ->innerJoin('FarpostStoreBundle:SchedulePart', 'sp', Join::WITH, 's.schedule_part = sp.id')
         ->innerJoin('FarpostStoreBundle:Group',        'g',  Join::WITH, 'sp.group = g.id');
      return $qb;
   }

   private function _finalizeUpdate(&$recs)
   {
      $recs = array_map(function ($v) {
         $elem = $v['0'];
         $elem['status'] = $v['status'];
         return $elem;
      }, $recs);
      return $recs;
   }

   private function _finalizeForGroup(&$recs)
   {
      $result = [];
      foreach($recs as &$rec) {
         $elem = [
            'id' => $rec->getId(),
            'type_id' => is_null($rec->getGeoObjectType()) ? null : $rec->getGeoObjectType()->getId(),
            'building_id' => is_null($rec->getBuilding()) ? null : $rec->getBuilding()->getId(),
            'alias' => $rec->getAlias(),
            'lat' => $rec->getLat(),
            'lon' => $rec->getLon(),
            'cataloged' => $rec->getCataloged(),
            'status' => $rec->getStatus(),
            'level' => $rec->getLevel()
         ];
         array_push($result, $elem);
      }
      return $result;
   }

   private function _finalize(&$recs)
   {
      $recs = array_map(function ($v) {
         $type_id = $v['geoobject_type_id'];
         // $type_id = 1;
         unset($v['geoobject_type_id']);
         $v['type_id'] = $type_id;
         return $v;
      }, $recs);
      return $recs;
   }

   public function getForGroup($group_id)
   {
      $recs = $this->_prepareQB()
                   ->where('g.id = :group_id')
                   ->setParameter('group_id', $group_id)
                   ->getQuery()
                   ->getResult();
      return $this->_finalizeForGroup($recs);
   }

   public function getUpdate($last_time, $group_id)
   {
      $recs = $this->_prepareQB()
                  ->select('go, lm.status')
                  ->innerJoin('FarpostStoreBundle:LastModified', 'lm', Join::WITH, 'lm.record_id = go.id')
                  ->where('lm.table_name = :table_name')
                  ->andWhere('lm.last_modified > :time')
                  ->andWhere('g.id = :group_id')
                  ->setParameter('table_name', 'auditories')
                  ->setParameter('time', $last_time)
                  ->setParameter('group_id', $group_id)
                  ->getQuery()
                  ->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true)
                  ->getArrayResult();
      $this->_finalizeUpdate($recs);
      $this->_finalize($recs);
      return $recs;
   }

   public function synchronizeWith($items)
   {
      $em = $this->getEntityManager();
      $go_repo = $em->getRepository('FarpostStoreBundle:GeoObject');
      $fake_recs = $go_repo->findBy(['cataloged' => 0]);

      foreach($fake_recs as &$fake_rec) {
         $em->remove($fake_rec);
         $em->flush();
      }
      $batch_size = 100;
      $i = 0;
      $qb = $this->_prepareQB();
      foreach ($items as &$item) {
         $i++;
         $is_new = false;
         try {
            $twin = $go_repo->findOneBy(['id' => $item['id']]);
            if (is_null($twin)) {
               throw new \Doctrine\ORM\NoResultException();
            }
         }
         catch (\Doctrine\ORM\NoResultException $e) {
            $twin = new GeoObject();
            $twin->setId($item['id']);
            $is_new = true;
         }
         if ($item['type_id']) {
            $go_type = $em->getReference(
               'FarpostStoreBundle:GeoObjectType',
               $item['type_id']
            );
            $twin->setGeoobjectType($go_type);
         } else {
            // echo "<p>In table GeoObjects: no 'type_id' for record with 'id' = $item[id]</p>";
         }
         if ($item['building_id']) {
            $go_building = $em->getReference(
               'FarpostStoreBundle:Building',
               $item['building_id']
            );
            $twin->setBuilding($go_building);
         } else {
            // echo "<p>In table GeoObjects: no 'building_id' for record with 'id' = $item[id]</p>";
         }
         $twin->setAlias($item['alias'])
              ->setLevel($item['level'])
              ->setLon($item['lon'])
              ->setLat($item['lat'])
              ->setStatus($item['status'])
              ->setCataloged(1);
         if ($is_new) {
            $em->persist($twin);
         } else {
            $em->merge($twin);
         }
         if (($i % $batch_size) === 0) {
            $em->flush();
            $this->clear();
         }
      }
      $em->flush();
      $this->clear();
   }

   public function syncValue($alias)
   {
      $geoobject = $this->findOneBy(['alias' => $alias]);
      if (!is_null($geoobject)) {
         return $geoobject;
      }
      $new_id = $this->_em->createQueryBuilder()->select('MAX(go.id)')
                          ->from('FarpostStoreBundle:GeoObject', 'go')
                          ->getQuery()
                          ->getSingleResult();
      echo json_encode($new_id);
      if (is_null($new_id)) {
         $new_id = 1;
      } else {
         $new_id = $new_id[1] + 10;
      }
      $geoobject = new GeoObject();
      $geoobject->setId($new_id)->setAlias($alias)->setCataloged(0)->setStatus(1);
      $this->_em->persist($geoobject);
      $this->_em->flush();
      return $geoobject;
   }

}