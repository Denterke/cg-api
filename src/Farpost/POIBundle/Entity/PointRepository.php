<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 03/08/15
 * Time: 10:55
 */

namespace Farpost\POIBundle\Entity;


use Doctrine\ORM\EntityRepository;

class PointRepository extends EntityRepository
{
    public function findByTypeGroup($groupId)
    {
        $qb = $this->createQueryBuilder('p')
            ->innerJoin('FarpostPOIBundle:Type', 't', 'WITH', 'p.type = t.id')
            ->where('t.group = :group')
            ->setParameter('group', $groupId)
        ;

        return $qb->getQuery()->getResult();
    }

}