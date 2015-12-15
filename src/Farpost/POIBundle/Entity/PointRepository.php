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
    protected function getActualQB()
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.startAt <= CURRENT_TIMESTAMP()')
            ->andWhere('p.endAt >= CURRENT_TIMESTAMP()')
        ;

        return $qb;
    }

    public function findActualByTypeGroup($groupId)
    {
        $qb = $this->getActualQB()
            ->innerJoin('FarpostPOIBundle:Type', 't', 'WITH', 'p.type = t.id')
            ->where('t.group = :group')
            ->setParameter('group', $groupId)
        ;

        return $qb->getQuery()->getResult();
    }

    public function findActualByType($typeId)
    {
        $qb = $this->getActualQB()
            ->innerJoin('FarpostPOIBundle:Type', 't', 'WITH', 'p.type = t.id')
            ->where('p.type = :type')
            ->setParameter('type', $typeId)
        ;

        return $qb->getQuery()->getResult();
    }

    public function findActualAll()
    {
        $qb = $this->getActualQB()
            ->innerJoin('FarpostPOIBundle:Type', 't', 'WITH', 'p.type = t.id');

        return $qb->getQuery()->getResult();
    }

}