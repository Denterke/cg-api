<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 16/07/15
 * Time: 10:40
 */

namespace Farpost\MapsBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;

class EdgePointRepository extends EntityRepository
{
    public function copyFrom(EntityManager $src)
    {
        $q = $src->createQuery('select psp from FarpostBackUpBundle:PathSegmentPoint psp');
        $it = $q->iterate();
        $batchSize = 20;
        $i = 0;
        foreach($it as $row) {
            $srcEdgePoint = $row[0];
            $edgePoint = new EdgePoint();
            $edgePoint->setId($srcEdgePoint->getId())
                ->setLon($srcEdgePoint->getLon())
                ->setLat($srcEdgePoint->getLat())
                ->setSeq($srcEdgePoint->getIdx())
                ->setEdge($this->_em->getReference('FarpostMapsBundle:Edge', $srcEdgePoint->getPathSegment()->getId()))
            ;
            $this->_em->persist($edgePoint);
            if (++$i % $batchSize === 0) {
                $this->_em->flush();
                $this->_em->clear();
            }
        }
        $this->_em->flush();
    }
}