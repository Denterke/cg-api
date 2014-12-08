<?php

namespace Farpost\StoreBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/*
 * NewsRepository
 */
class NewsRepository extends EntityRepository
{
   private function _prepareQB()
   {
      $qb = $this->_em->createQueryBuilder();
      $qb->select('n')
         ->from('FarpostStoreBundle:News', 'n');
      return $qb;
   }

   public function getNews($start, $count, $hostname)
   {
      $qb = $this->_prepareQB()->where('n.active = true')
         ->setMaxResults(abs($count));
      if ($start == -1) {
         $recs = $qb->orderBy('n.id', 'DESC')
            ->getQuery()
            ->getResult();
      } else {
         $sign = $count >= 0 ? " >= " : " <= ";
         $recs = $qb->andWhere("n.id $sign :start")
            ->setParameter('start', $start)
            ->orderBy('n.id', 'ASC')
            ->getQuery()
            ->getResult();
      }
      $result = [];
      foreach ($recs as &$rec) {
         $elem = [
            'date' => $rec->getDt(),
            'body' => $rec->getBody(),
            'title'=> $rec->getTitle() ? $rec->getTitle() : '',
            'id'   => $rec->getId()
         ];
         $imgs = $rec->getImages();
         $imgElems = [];
         foreach($imgs as &$img) {
            $imgElem = [
               'src'       => $img->getSrcURL($hostname),
               'src_big'   => $img->getSrcBigURL($hostname),
               'src_small' => $img->getSrcSmallURL($hostname),
               'width'     => $img->getWidth(),
               'height'    => $img->getHeight()
            ];
            $imgElems[] = $imgElem;
         }
         $elem['images'] = $imgElems;
         $link = $rec->getLinks();
         $linkElems = [];
         foreach($linkElems as &$linkElem) {
            $linkElem = [
               'url'   => $linkElem->getUrl($hostname),
               'title' => $linkElem->getTitle()
            ];
            $linkElems[] = $linkElem;
         }
         if (count($linkElems) > 0) {
            $elem['link'] = $linkElems[0];
         } else {
            $elem['link'] = null;
         }
         $result[] = $elem;
      }
      return $result;

      
   }

   public function getForGroup($group_id)
   {
      $qb = $this->_prepareQB();
      return $qb->where('g.id = :group_id')
                 ->setParameter('group_id', $group_id)
                 ->getQuery()
                 ->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true)
                 ->getArrayResult();
   }

   public function getUpdate($last_time, $group_id)
   {
      return $this->_prepareQB()
                  ->select('u.id, u.first_name, u.last_name, u.middle_name, lm.status')
                  ->innerJoin('FarpostStoreBundle:LastModified', 'lm', Join::WITH, 'lm.record_id = u.id')
                  ->where('lm.table_name = :table_name')
                  ->andWhere('lm.last_modified > :time')
                  ->andWhere('g.id = :group_id')
                  ->setParameter('table_name', 'users')
                  ->setParameter('time', $last_time)
                  ->setParameter('group_id', $group_id)
                  ->getQuery()
                  ->getArrayResult();
   }
}