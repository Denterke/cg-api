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

class EdgeRepository extends EntityRepository
{
    public function copyFrom(EntityManager $src)
    {
        $q = $src->createQuery('select ps from FarpostBackUpBundle:PathSegment ps');
        $it = $q->iterate();
        $batchSize = 20;
        $i = 0;
        foreach($it as $row) {
            $srcEdge = $row[0];
            $edge = new Edge();
            $edge->setId($srcEdge->getId())
                ->setFromNode($this->_em->getReference('FarpostMapsBundle:Node', $srcEdge->getObjectFrom()->getId()))
                ->setToNode($this->_em->getReference('FarpostMapsBundle:Node', $srcEdge->getObjectTo()->getId()))
                ->setWeight(0);
            ;
            if ($srcEdge->getLevel() !== null) {
                $edge->setLevel($this->_em->getReference('FarpostMapsBundle:Level', $srcEdge->getLevel()));
            }
            $this->_em->persist($edge);
            if (++$i % $batchSize === 0) {
                $this->_em->flush();
                $this->_em->clear();
            }
        }
        $this->_em->flush();
    }
}