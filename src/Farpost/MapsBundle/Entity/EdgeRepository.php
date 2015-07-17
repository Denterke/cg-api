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

class EdgeRepository extends EntityRepository
{
    public function copyFrom(EntityManager $src)
    {
        $this->_em->getConnection()->getConfiguration()->setSQLLogger(null);
        
        $q = $src->createQuery('select ps from FarpostBackUpBundle:PathSegment ps');
        $it = $q->iterate();
        $batchSize = 1000;
        $i = 0;
        foreach($it as $row) {
            $srcEdge = $row[0];
            $edge = new Edge();
            $edge->setId($srcEdge->getId())
                ->setFromNode($this->_em->getReference('FarpostMapsBundle:Node', $srcEdge->getObjectFrom()->getId()))
                ->setToNode($this->_em->getReference('FarpostMapsBundle:Node', $srcEdge->getObjectTo()->getId()))
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
        $this->_em->clear();
    }

    public function calcEdgeWeight(Edge $edge)
    {
        $calcDistance = function($a, $b) {
            $earthRadius = 6371000; //meters
            $dLat = deg2rad($b['lat'] - $a['lat']);
            $dLon = deg2rad($b['lon'] - $a['lon']);
            $v = sin($dLat / 2) * sin($dLat / 2) +
                cos(deg2rad($a['lat'])) * cos(deg2rad($b['lat'])) *
                sin($dLon / 2) * sin($dLon / 2);
            $c = 2 * atan2(sqrt($v), sqrt(1 - $v));
            $dist = $c * $earthRadius;
            if (is_nan($dist)) {
                $dist = 0;
            }
            return $dist;
        };

        $distance = 0;
        $source = $edge->getFromNode();
        $target = $edge->getToNode();
        $edgePoints = $edge->getPoints();
        $points = [
            [
                'lat' => $source->getLat(),
                'lon' => $source->getLon()
            ]
        ];

        for ($i = 0; $i < $edgePoints->count(); $i++) {
            array_push($points, [
                'lat' => $edgePoints[$i]->getLat(),
                'lon' => $edgePoints[$i]->getLon()
            ]);
        }
        array_push($points, [
            'lat' => $target->getLat(),
            'lon' => $target->getLon()
        ]);
        for ($i = 0; $i < count($points) - 1; $i++) {
            $distance += $calcDistance($points[$i + 1], $points[$i]);
        }
        if ($distance == 0) {
            $distance = 1;
        }
        if ($source->getType()->getId() === $target->getType()->getId()) {
            $type = $source->getType()->getId();
            switch ($type) {
                case NodeType::ESCALATOR:
                case NodeType::ELEVATOR:
                case NodeType::STAIR:
                    $weight = $distance * NodeType::$WEIGHTS[$type];
                    break;
                default:
                    $weight = $distance * NodeType::FRACTION_SHIFT;
            }
        } else {
            $weight = $distance * NodeType::FRACTION_SHIFT;
        }
        return [
            'weight' => $weight,
            'distance' => $distance
        ];
    }

    public function normalize()
    {
        $q = $this->_em->createQuery('select e from FarpostMapsBundle:Edge e');
        $it = $q->iterate();
        $batchSize = 20;
        $i = 0;
        foreach($it as $row) {
            $edge = $row[0];
            $processed[] = $edge;
            if ($edge->getFromNode()->getId() === $edge->getToNode()->getId()) {
                $this->_em->remove($edge);
            } else {
                $vals = $this->calcEdgeWeight($edge);
                $edge->setWeight($vals['weight'])
                    ->setDistance($vals['distance']);
            }
            if (++$i % $batchSize === 0) {
                $this->_em->flush();
                $this->_em->clear();
            }

        }
        $this->_em->flush();
    }
}