<?php

namespace Farpost\StoreBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\DBAL\Cache\QueryCacheProfile;
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

   // private function writeTime($str)
   // {
      // $dt = new \Datetime();
      // error_log("$str:" . $dt->getTimestamp());
   // }

   public function realizeFake(&$fakes)
   {
      $pdo = $this->_em->getConnection();
      $stmt = $pdo->prepare(
         "SELECT
            id, alias
          FROM
            geoobjects
          WHERE
            geoobject_type_id = " . self::AUDITORY_TYPE_ID);
      $stmt->execute();
      $objs = [];
      while ($row = $stmt->fetch()) {
         $objs[$row['alias']] = $row;
      }
      // print_r($objs);
      $stmt = $pdo->prepare("SELECT max(id) FROM geoobjects;");
      $stmt->execute();
      $curId = $stmt->fetchAll();
      $curId = $curId[0]['max'];
      $curId = $curId ? $curId + 1: 1;
      $keys = array_keys($objs);
      // $this->writeTime("before insert");
      $insStr = 
         "INSERT INTO
            geoobjects
            (id, alias, cataloged, status, geoobject_type_id)
          VALUES";
      $firstIns = true;
      $resRefs = [];
      for ($i = 0; $i < count($fakes); $i++) {
         $objIdx = array_search($fakes[$i], $keys);
         if ($objIdx === false) {
            $insStr .= $firstIns ? ' ' : ', ';
            $firstIns = false;
            $insStr .= "($curId, '{$fakes[$i]}', 0, 1, " . self::AUDITORY_TYPE_ID . ")";
            array_push($resRefs, $i);
            $curId++;
         } else {
            $fakes[$i] = $objs[$fakes[$i]]['id'];
         }
      }
      // $this->writeTime("after loop");
      if (!$firstIns) {
         $insStr .= " returning id";
         // echo $insStr;
         // exit;
         $stmt = $pdo->prepare($insStr);
         $stmt->execute();
         $ids = $stmt->fetchAll();
         // print_r($ids);
         // exit;
         for ($i = 0; $i < count($ids); $i++) {
            $fakes[$resRefs[$i]] = $ids[$i]['id'];
         }
      }
   }

   private function normalize(&$item)
   {
      $item['type_id']     = $item['type_id']     ?: 0;
      $item['building_id'] = $item['building_id'] ?: 'null';
      $item['level']       = $item['level']       ?: 'null';
      $item['lon']         = $item['lon']         ?: 'null';
      $item['lat']         = $item['lat']         ?: 'null';
      if ($item['type_id'] == self::AUDITORY_TYPE_ID && rtrim($item['alias']) == '') {
         $item['type_id'] = 0;
      }      
   }

   public function synchronizeWith($items)
   {
      echo "IN SYNC\n";
      $pdo = $this->_em->getConnection();
      $stmt = $pdo->prepare(
         "DELETE FROM
            geoobjects
          WHERE
            cataloged = 0"
      );
      $stmt->execute();
      $stmt = $pdo->prepare(
         "SELECT
            id, geoobject_type_id as type_id, building_id, alias, level, lon, lat, status
          FROM
            geoobjects
         "
      );
      $stmt->execute();
      $objs = [];
      while ($row = $stmt->fetch()) {
         $objs[$row['id']] = $row;
      }
      $ids = array_keys($objs);
      $insStr = 
         "INSERT INTO
            geoobjects (id, geoobject_type_id, building_id, alias, level, lon, lat, cataloged, status)
          VALUES ";
      $updStr = "";
      $firstIns = true;
      // $this->writeTime("before loop");

      foreach ($items as &$item) {
         unset($item['node_id']);
         $idx = array_search($item['id'], $ids);
         $this->normalize($item);
         if ($idx !== false) {
            $this->normalize($objs[$ids[$idx]]);
         }
         if ($idx === false) {
            $insStr .= $firstIns ? '' : ', ';
            $firstIns = false;
            $insStr .= "({$item['id']}, {$item['type_id']}, {$item['building_id']}, " .
                       "'{$item['alias']}', {$item['level']}, {$item['lon']}, {$item['lat']}, " .
                       "1, {$item['status']})";
         } else if (empty(array_diff_assoc($item, $objs[$ids[$idx]]))) {
            continue;
         } else {
            // print_r($item);
            // print_r($objs[$ids[$idx]]);
            // exit;
            $updStr = 
               "UPDATE 
                  GEOOBJECTS
               SET
                  geoobject_type_id = {$item['type_id']},
                  building_id = {$item['building_id']},
                  alias = '{$item['alias']}',
                  level = {$item['level']},
                  lon = {$item['lon']},
                  lat = {$item['lat']},
                  cataloged = 1,
                  status = {$item['status']}
               WHERE
                  id = {$item['id']};
               ";
               $stmt = $pdo->prepare($updStr);
               $stmt->execute();
         }
      }
      // $this->writeTime("after loop");
      if (!$firstIns) {
         // $this->writeTime("before insert2");
         $stmt = $pdo->prepare($insStr);
         $stmt->execute();
         // $this->writeTime("after insert");
      } 
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