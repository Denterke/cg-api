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

class NodeTypeRepository extends EntityRepository
{
    public function copyFrom(EntityManager $src)
    {
        $q = $src->createQuery('select ot from FarpostBackUpBundle:ObjectType ot');
        gc_enable();
        $it = $q->iterate();
        $batchSize = 20;
        $i = 0;
        foreach($it as $row) {
            $srcNodeType = $row[0];
            $nodeType = new NodeType();
            $nodeType->setId($srcNodeType->getId())
                ->setAlias($srcNodeType->getAlias())
            ;
            $this->_em->persist($nodeType);
            unset($srcNodeType);
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
}