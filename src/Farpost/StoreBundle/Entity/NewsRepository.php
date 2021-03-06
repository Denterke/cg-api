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
         $recs = $qb->andWhere("n.id < :start")
            ->setParameter('start', $start)
            ->orderBy('n.id', 'DESC')
            ->getQuery();
         // echo $recs->getDQL() . "\n";
         // echo $start . "\n";
         // echo $count . "\n";
         $recs = $recs->getResult();
            // ->getDQL();
         // echo $recs;
            // ->getResult();
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
}