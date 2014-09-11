<?php

namespace Farpost\StoreBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/*
 * StudySetRepository
 */
class StudySetRepository extends EntityRepository
{
   private function _prepareQB()
   {
      $qb = $this->_em->createQueryBuilder();
      $qb->select('ss')
         ->distinct()
         ->from('FarpostStoreBundle:StudySet', 'ss');
         // ->innerJoin('FarpostStoreBundle:Specialization', 'sp',  Join::WITH, 'ss.specialization = sp.id')
         // ->innerJoin('FarpostStoreBundle:Course', 'c', Join::WITH, 'ss.course = c.id')
         // ->innerJoin('FarpostStoreBundle:Department', 'd');
      return $qb;
   }

   public function syncValue($specialization, $course, $department)
   {
      $qb = $this->_prepareQB();
      try {
         $study_set = $qb->where($qb->expr()->eq('ss.specialization', '?1'))
                         ->andWhere($qb->expr()->eq('ss.course', '?2'))
                         ->andWhere('?3 MEMBER OF ss.departments')
                         ->setParameter(1, $specialization->getId())
                         ->setParameter(2, $course->getId())
                         ->setParameter(3, $department->getId())
                         ->getQuery()
                         ->getSingleResult();
      }
      catch (\Doctrine\ORM\NoResultException $e) {
         error_log("study set no result");
         $study_set = new StudySet();
         $study_set->setSpecialization($specialization)
                   ->setCourse($course)
                   ->addDepartment($department);
         $this->_em->persist($study_set);
         $this->_em->flush();
         return $study_set;
      }
      return $study_set;
   }
}