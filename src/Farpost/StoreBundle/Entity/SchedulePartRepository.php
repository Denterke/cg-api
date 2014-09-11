<?php

namespace Farpost\StoreBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/*
 * SchedulePartRepository
 */
class SchedulePartRepository extends EntityRepository
{
   private function _prepareQB()
   {
      $qb = $this->_em->createQueryBuilder()
                 ->select('sp')
                 ->from('FarpostStoreBundle:SchedulePart', 'sp');
      return $qb;
   }

   public function syncValue($user, $discipline, $group)
   {
      $qb = $this->_prepareQb();
      try {
         $schedule_part = $qb->where($qb->expr()->eq('sp.professor', '?1'))
                             ->andWhere($qb->expr()->eq('sp.discipline', '?2'))
                             ->andWhere($qb->expr()->eq('sp.group', '?3'))
                             ->setParameter(1, $user->getId())
                             ->setParameter(2, $discipline->getId())
                             ->setParameter(3, $group->getId())
                             ->getQuery()
                             ->getSingleResult();
      }
      catch (\Exception $e) {
         $schedule_part = new SchedulePart();
         $schedule_part->setProfessor($user)
                       ->setGroup($group)
                       ->setDiscipline($discipline);
         $this->_em->persist($schedule_part);
         $this->_em->flush();
         return $schedule_part;
      }
      return $schedule_part;
   }
}