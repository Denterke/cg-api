<?php

namespace Farpost\StoreBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/*
 * DepartmentRepository
 */
class DepartmentRepository extends EntityRepository
{
   private function _prepareQB()
   {
      $qb = $this->_em->createQueryBuilder();
      $qb->select('d')
         ->distinct()
         ->from('FarpostStoreBundle:Department', 'd');
      return $qb;
   }

   public function syncValue($alias, $school, $study_type)
   {
      $qb = $this->_em->createQueryBuilder()->select('d')->from('FarpostStoreBundle:Department', 'd');
      try {
         $department = $qb->where($qb->expr()->eq('d.school', '?1'))
                          ->andWhere($qb->expr()->eq('d.study_type', '?2'))
                          ->andWhere('d.alias = ?3')
                          ->setParameter(1, $school->getId())
                          ->setParameter(2, $study_type->getId())
                          ->setParameter(3, $alias)
                          ->getQuery()
                          ->getSingleResult();
      }
      catch (\Doctrine\ORM\NoResultException $e) {
         $department = new Department();
         $department->setAlias($alias)
                    ->setSchool($school)
                    ->setStudyType($study_type);
         $this->_em->persist($department);
         $this->_em->flush();
         return $department;
      }
      return $department;
   }
}