<?php

namespace Farpost\StoreBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/*
 * ConfigRepository
 */
class ConfigRepository extends EntityRepository
{
   private function _prepareQB()
   {
      $qb = $this->_em->createQueryBuilder();
      $qb->select('c')
         ->distinct()
         ->from('FarpostStoreBundle:Config', 'c');
      return $qb;
   }

	public function getLastWipeTS()
	{
		$qb = $this->_prepareQB();
		try {
			$result = $qb
				->where('c.param = :param')
				->setParameter('param', 'wipeTS')
				->getQuery()
				->getSingleResult();
			return $result->getValue();
		}
		catch (\Doctrine\ORM\NoResultException $e) {
			return 0;
		}
	}
}