<?php

namespace Farpost\StoreBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/*
 * StudyTypeRepository
 */
class StudyTypeRepository extends EntityRepository
{
   public function syncValue($alias)
   {
      $study_type = $this->findOneBy(['alias' => $alias]);
      if (!is_null($study_type)) {
         return $study_type;
      }
      $study_type = new StudyType();
      $study_type->setAlias($alias);
      $this->_em->persist($study_type);
      $this->_em->flush();
      return $study_type;
   }
}