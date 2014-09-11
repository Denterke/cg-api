<?php

namespace Farpost\StoreBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/*
 * LessonTypeRepository
 */
class LessonTypeRepository extends EntityRepository
{
   public function syncValue($alias)
   {
      $lesson_type = $this->findOneBy(['alias' => $alias]);
      if (!is_null($lesson_type)) {
         return $lesson_type;
      }
      $lesson_type = new LessonType();
      $lesson_type->setAlias($alias);
      $this->_em->persist($lesson_type);
      $this->_em->flush();
      return $lesson_type;
   }
}