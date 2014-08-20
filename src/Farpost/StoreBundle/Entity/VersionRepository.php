<?php

namespace Farpost\StoreBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/*
 *VersionRepository
 */
class VersionRepository extends EntityRepository
{
   //select * from versions v where (select count(*) from versions where type = v.type and v_datetime > v.v_datetime) < 1;
   private function _prepareQB()
   {
      $qb = $this->_em->createQueryBuilder();
      $qb->select('v')
         ->from('FarpostStoreBundle:Version', 'v');
      $subquery = $this->_em->createQueryBuilder()
                       ->select('count(a.id)')
                       ->from('FarpostStoreBundle:Version', 'a')
                       ->where('a.type = v.type')
                       ->andWhere('a.v_datetime > v.v_datetime');
      $qb->where('(' . $subquery->getDQL() . ') < 1');
      return $qb;
   }

   private function _finalize(&$recs, $hostname)
   {
      $result = [];
      $plans = [];
      foreach($recs as &$rec) {
         $dt = date('Ymd', $rec['v_datetime']);
         $path = 'http://' . $hostname . '/static/' . $rec['base'];
         $level = $rec['type'];
         $elem = [
            'version' => $dt,
            'source'  => $path
         ];
         if ($level == -20) {
            $result['catalog'] = $elem;
            continue;
         }
         $elem['level'] = $level;
         array_push($plans, $elem);
      }
      if (count($plans) > 0) {
         $result['plans'] = $plans;
      }
      // echo json_encode($plans);
      $recs = $result;
   }

   private function _finalizeWeb(&$recs)
   {
      $result = [];
      $used = [-20 => 0];
      for ($i = 2; $i <= 12; $i++) {
         $used[$i] = 0;
      }
      foreach ($recs as &$rec) {
         $dt = date('d-m-Y, G:i:s', $rec['v_datetime']);
         $level = "План уровня " . $rec['type'];
         if ($rec['type'] == -20) {
            $level = "Каталог организаций";
         }
         $version = $dt;
         $elem = [
            "version" => $dt,
            "type"    => $level,
            "type_id" => $rec['type']
         ];
         $used[$rec['type']] = 1;
         array_push($result, $elem);
      }
      foreach($used as $key=>$val) {
         if ($val == 0) {
            $elem = [
               "version" => "Нет базы",
               "type"    => "План уровня " . $key,
               "type_id" => $key
            ];
            if ($key == -20) {
               $elem["type"] = "Каталог организаций";
            }
            array_push($result, $elem);
         }
      }
      return $result;
   }

   public function getBases($hostname)
   {
      $recs = $this->_prepareQB()->getQuery()->getArrayResult();
      $this->_finalize($recs, $hostname);
      return $recs;
   }

   public function getForWeb()
   {
      $recs = $this->_prepareQB()->getQuery()->getArrayResult();
      return $this->_finalizeWeb($recs);
   }
}