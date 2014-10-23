<?php

namespace Farpost\StoreBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/*
 * SchoolRepository
 */
class GroupRepository extends EntityRepository
{
   private function _prepareQB()
   {
      $qb = $this->_em->createQueryBuilder();
      $qb->select('g')
         ->distinct()
         ->from('FarpostStoreBundle:Group', 'g');
      return $qb;
   }

   private function _finalizeList(&$recs)
   {
      $result = [];
      foreach($recs as &$rec) {
         $elem = [
            'id' => $rec->getId(),
            'alias' => $rec->getAlias(),
            'department' => $rec->getStudySet()->getDepartment()->getAlias()
         ];
         array_push($result, $elem);
      }
      return $result;
   }

   public function getList($t)
   {
      $dt = new \Datetime();
      $dt->setTimestamp($t);
      $recs = $this->_prepareQB()
                   ->innerJoin('FarpostStoreBundle:LastModified', 'l', Join::WITH, 'g.id = l.record_id')
                   ->where('l.table_name = :table_name')
                   ->andWhere('l.last_modified >= :time')
                   ->andWhere('l.status = 1')
                   ->setParameter('table_name', 'groups')
                   ->setParameter('time', $dt)
                   ->getQuery()
                   ->getResult();
      return $this->_finalizeList($recs);
   }

   public function syncValue($alias, $study_set)
   {
      $qb = $this->_prepareQB();
      try {
         $group = $qb->where('g.alias = ?1')
                     ->andWhere($qb->expr()->eq('g.study_set', '?2'))
                     ->setParameter(1, $alias)
                     ->setParameter(2, $study_set->getId())
                     ->getQuery()
                     ->getSingleResult();
         $qb2 = $this->_em->createQueryBuilder();
         $qb2->update('FarpostStoreBundle:LastModified', 'lm')
                    ->set('lm.group_id', ':group_id2')
                    ->where($qb2->expr()->eq('lm.table_name', ':s_table'))
                    ->setParameter('s_table', 'schedule_rendered')
                    ->setParameter('group_id2', $group->getId())
                    ->andWhere(
                         $qb2->expr()->in(
                            'lm.record_id',
                            $this->_em->createQueryBuilder()->select('sr.id')
                                ->from('FarpostStoreBundle:ScheduleRendered', 'sr')
                                ->innerJoin('FarpostStoreBundle:Schedule',     's',  Join::WITH, 'sr.schedule = s.id')
                                ->innerJoin('FarpostStoreBundle:SchedulePart', 'sp', Join::WITH, 's.schedule_part = sp.id')
                                ->innerJoin('FarpostStoreBundle:Group',        'g',  Join::WITH, 'sp.group = g.id')
                                ->innerJoin('FarpostStoreBundle:Semester',     'sm', Join::WITH, 's.semester = sm.id')
                                ->where($qb2->expr()->eq('sm.id', ':sem_id'))
                                ->andWhere($qb2->expr()->eq('g.id', ':group_id'))
                           ->getQuery()->getDQL()
                         )
                      )
                    ->setParameter('sem_id', 1)
                    ->setParameter('group_id', $group->getId())
                    ->getQuery()
                    // ->getDQL();
                    ->getResult();
         // exit; 
         $this->_em->createQueryBuilder()
                   ->delete('FarpostStoreBundle:SchedulePart', 'sp')
                   ->where($qb->expr()->eq('sp.group', '?1'))
                   ->setParameter(1, $group->getId())
                   ->getQuery()
                   ->getResult();
      }
      catch (\Doctrine\ORM\NoResultException $e) {
         error_log("group no result");
         $group = new Group();
         $group->setAlias($alias)->setStudySet($study_set);
         $this->_em->persist($group);
         $this->_em->flush();
         return $group;
      }
      return $group;
   }
}