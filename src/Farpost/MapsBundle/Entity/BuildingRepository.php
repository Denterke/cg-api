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

class BuildingRepository extends EntityRepository
{
    public function copyFrom(EntityManager $src)
    {
        $q = $src->createQuery('select b from FarpostBackUpBundle:Building b');
        $it = $q->iterate();
        $batchSize = 20;
        $i = 0;
        foreach($it as $row) {
            $srcBuilding = $row[0];
            $building = new Building();
            $building->setId($srcBuilding->getId())
                ->setAlias($srcBuilding->getAlias())
                ->setNumber($srcBuilding->getNumber())
                ->setLat($srcBuilding->getLat())
                ->setLon($srcBuilding->getLon())
            ;
            $this->_em->persist($building);
            if (++$i % $batchSize === 0) {
                $this->_em->flush();
                $this->_em->clear();
            }
        }
        $this->_em->flush();
    }
}