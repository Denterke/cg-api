<?php

namespace Farpost\StoreBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/*
 * SpecializationRepository
 */
class SpecializationRepository extends EntityRepository
{
   public function syncValue($alias)
   {
      $specialization = $this->findOneBy(['alias' => $alias]);
      if (!is_null($specialization)) {
         return $specialization;
      }
      $specialization = new Specialization();
      $specialization->setAlias($alias);
      $this->_em->persist($specialization);
      $this->_em->flush();
      return $specialization;
   }
}