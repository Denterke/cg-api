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
        $this->_em->getConfiguration()->setSQLLogger(null);
        gc_enable();
        $q = $src->createQuery('select psp from FarpostBackUpBundle:PathSegmentPoint psp');
        $it = $q->iterate();
        $batchSize = 20;
        $i = 0;
        while (($row = $it->next()) !== false) {
            $srcEdgePoint = $row[0];
            $edgePoint = new EdgePoint();
            $edge = $this->_em
                ->getRepository('FarpostMapsBundle:Edge')
                ->findOneById($srcEdgePoint->getPathSegment()->getId())
            ;
            $edgePoint->setId($srcEdgePoint->getId())
                ->setLon($srcEdgePoint->getLon())
                ->setLat($srcEdgePoint->getLat())
                ->setSeq($srcEdgePoint->getIdx())
                ->setEdge($edge)
            ;
            $this->_em->persist($edgePoint);
            unset($edgePoint);
            unset($edge);
            unset($srcEdgePoint);
            if (++$i % $batchSize === 0) {
                $this->_em->flush();
                $this->_em->clear();
                $src->clear();
                gc_collect_cycles();
            }
        }
        $this->_em->flush();
        $this->_em->clear();
        $src->clear();
        gc_collect_cycles();
    }
}