<?php

namespace Farpost\StoreBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/*
 *UserRepository
 */
class UserRepository extends EntityRepository
{
   private function _prepareQB()
   {
      $qb = $this->_em->createQueryBuilder();
      $qb->select('u.id, u.first_name, u.last_name, u.middle_name')
         ->distinct()
         ->from('FarpostStoreBundle:User', 'u')
         ->innerJoin('FarpostStoreBundle:SchedulePart', 'sp', Join::WITH, 'u.id = sp.professor')
         ->innerJoin('FarpostStoreBundle:Group',        'g',  Join::WITH, 'sp.group = g.id');
      return $qb;
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

   public function syncValue($full_name)
   {
      // echo $full_name;
      list(
         $last,
         $first,
         $middle
      ) = explode(" ", $full_name);
      $professor = $this->findOneBy([
         'first_name' => $first,
         'middle_name' => $middle,
         'last_name' => $last
      ]);
      if (!is_null($professor)) {
         return $professor;
      }
      $professor = new User();
      $professor->setFirstName($first)
                ->setLastName($last)
                ->setMiddleName($middle);
      $this->_em->persist($professor);
      $this->_em->flush();
      return $professor;
   }
}