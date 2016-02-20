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
        $this->_em->getConfiguration()->setSQLLogger(null);
        gc_enable();
        $q = $src->createQuery('select ps from FarpostBackUpBundle:PathSegment ps');
        $it = $q->iterate();
        $batchSize = 20;
        $i = 0;
        foreach($it as $row) {
            $srcEdge = $row[0];
            if (!$srcEdge->getObjectFrom() || !$srcEdge->getObjectTo()) {
                unset($srcEdge);
                continue;
            }
            $edge = new Edge();
            $edge->setId($srcEdge->getId())
                ->setFromNode($this->_em->getReference('FarpostMapsBundle:Node', $srcEdge->getObjectFrom()->getId()))
                ->setToNode($this->_em->getReference('FarpostMapsBundle:Node', $srcEdge->getObjectTo()->getId()))
            ;
            if ($srcEdge->getLevel() !== null) {
                $edge->setLevel($this->_em->getReference('FarpostMapsBundle:Level', $srcEdge->getLevel()));
            }
            $this->_em->persist($edge);
            unset($edge);
            unset($srcEdge);
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
        $this->_em->getConfiguration()->setSQLLogger(null);
        gc_enable();
        $q = $this->_em->createQuery('select e from FarpostMapsBundle:Edge e');
        $it = $q->iterate();
        $batchSize = 20;
        $i = 0;
        $processed = [];
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
                for ($j = 0; $j < count($processed); $j++) {
                    unset($processed[$i]);
                }
                $processed = [];
                gc_collect_cycles();
            }
        }
        $this->_em->flush();
        $this->_em->clear();
        for ($j = 0; $j < count($processed); $j++) {
            unset($processed[$i]);
        }
        unset($processed);
        gc_collect_cycles();

        //delete reverse edges
        $sql = "
          DELETE FROM
            map_edges
          WHERE
            id in (
              select DISTINCT GREATEST(e1.id, e2.id)
                FROM map_edges e1
                INNER JOIN map_edges e2 ON e1.node_from_id = e2.node_to_id and e1.node_to_id = e2.node_from_id
            )
         ";
        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->execute();
    }
}