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

   public function getBases($hostname)
   {
      $recs = $this->_prepareQB()->getQuery()->getArrayResult();
      $this->_finalize($recs, $hostname);
      return $recs;
   }
}