<?php

namespace Farpost\StoreBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/*
 * CourseRepository
 */
class CourseRepository extends EntityRepository
{
   public function syncValue($alias)
   {
      $course = $this->findOneBy(['alias' => $alias]);
      if (!is_null($course)) {
         return $course;
      }
      $course = new Course();
      $course->setAlias($alias);
      $this->_em->persist($course);
      $this->_em->flush();
      return $course;
   }
}