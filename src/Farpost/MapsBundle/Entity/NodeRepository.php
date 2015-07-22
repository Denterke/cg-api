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
use Doctrine\ORM\Events;

class NodeRepository extends EntityRepository
{
    public function copyFrom(EntityManager $src)
    {
        $this->_em->getConfiguration()->setSQLLogger(null);
        gc_enable();
        $q = $src->createQuery('select o from FarpostBackUpBundle:Object o');
        $it = $q->iterate();
        $batchSize = 20;
        $i = 0;
        foreach($it as $row) {
            $srcNode = $row[0];
            $node = new Node();
            $node->setId($srcNode->getId())
                ->setLon($srcNode->getLon())
                ->setLat($srcNode->getLat())
                ->setAlias($srcNode->getAlias())
                ->setBuilding($this->_em->getReference('FarpostMapsBundle:Building', $srcNode->getBuilding()->getId()))
                ->setLevel($this->_em->getReference('FarpostMapsBundle:Level', $srcNode->getLevel()))
            ;
            $nodeType = $srcNode->getObjectType()
                ? $srcNode->getObjectType()->getId()
                : 0;
            $node->setType($this->_em->getReference('FarpostMapsBundle:NodeType', $nodeType));
            $this->_em->persist($node);
            unset($node);
            unset($srcNode);
            unset($nodeType);
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

    public function getNodesForLevel($level)
    {
        $this->_em->getConfiguration()->setSQLLogger(null);
        gc_enable();
        $it = $this->_em->createQuery('select n from FarpostMapsBundle:Node n where n.level = :level')
            ->setParameter('level', $level)
            ->iterate();
        $batchSize = 20;
        $i = 0;
        $nodes = [];
        foreach($it as $row) {
            $node = $row[0];
            $nodes[$node->getId()] = [
                'lat' => $node->getLat(),
                'lon' => $node->getLon(),
                'alias' => $node->getAlias(),
                'id' => $node->getId(),
                'type' => [
                    'alias' => $node->getType()->getAlias(),
                    'id' => $node->getType()->getId()
                ]
            ];
            unset($node);
            if (++$i % $batchSize === 0) {
                $this->_em->clear();
                gc_collect_cycles();
            }
        }
        $this->_em->clear();
        gc_collect_cycles();
        return $nodes;
    }
}