<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 15/07/15
 * Time: 12:48
 */

namespace Farpost\MapsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
/**
 * Edges
 *
 * @ORM\Table(name="map_edges")
 * @ORM\Entity(repositoryClass="Farpost\MapsBundle\Entity\EdgeRepository")
 */
class Edge {

    static public $sqliteAnnotations = [
        'table' => 'path_segments',
        'fields' => [
            [
                'name' => '_id',
                'type' => 'INTEGER',
                'PK' => true,
                'nullable' => false,
                'RK' => '',
                'getter' => 'getId'
            ],
            [
                'name' => 'level_id',
                'type' => 'INTEGER',
                'PK' => false,
                'nullable' => true,
                'RK' => 'levels',
                'getter' => 'getLevel'
            ],
            [
                'name' => 'node_from_id',
                'type' => 'INTEGER',
                'PK' => false,
                'nullable' => false,
                'RK' => 'nodes',
                'getter' => 'getFromNode'
            ],
            [
                'name' => 'node_to_id',
                'type' => 'INTEGER',
                'PK' => false,
                'nullable' => false,
                'RK' => 'nodes',
                'getter' => 'getToNode'
            ],
            [
                'name' => 'weight',
                'type' => 'DOUBLE',
                'PK' => false,
                'nullable' => false,
                'RK' => '',
                'getter' => 'getWeight'
            ],
            [
                'name' => 'distance',
                'type' => 'DOUBLE',
                'PK' => false,
                'nullable' => false,
                'RK' => '',
                'getter' => 'getDistance'
            ]
        ]
    ];

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Level")
     * @ORM\JoinColumn(name="level_id", referencedColumnName="id", nullable=true)
     */
    protected $level;

    /**
     * @var Object
     *
     * @ORM\ManyToOne(targetEntity="Node")
     * @ORM\JoinColumn(name="node_from_id", referencedColumnName="id")
     */
    protected $fromNode;

    /**
     * @var Object
     *
     * @ORM\ManyToOne(targetEntity="Node")
     * @ORM\JoinColumn(name="node_to_id", referencedColumnName="id")
     */
    protected $toNode;

    /**
     * @var float
     *
     * @ORM\Column(name="weight", type="float", nullable=true)
     */
    protected $weight;

    /**
     * @var float
     *
     * @ORM\Column(name="distance", type="float", nullable=true)
     */
    protected $distance;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="EdgePoint", mappedBy="edge")
     */
    protected $points;

    public function __construct()
    {
        $this->points = new ArrayCollection();
        $this->weight = 0;
        $this->distance = 0;
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return Edge
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set weight
     *
     * @param \float $weight
     * @return Edge
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return \float 
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set level
     *
     * @param \Farpost\MapsBundle\Entity\Level $level
     * @return Edge
     */
    public function setLevel(\Farpost\MapsBundle\Entity\Level $level = null)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return \Farpost\MapsBundle\Entity\Level 
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set fromNode
     *
     * @param \Farpost\MapsBundle\Entity\Node $fromNode
     * @return Edge
     */
    public function setFromNode(\Farpost\MapsBundle\Entity\Node $fromNode = null)
    {
        $this->fromNode = $fromNode;

        return $this;
    }

    /**
     * Get fromNode
     *
     * @return \Farpost\MapsBundle\Entity\Node 
     */
    public function getFromNode()
    {
        return $this->fromNode;
    }

    /**
     * Set toNode
     *
     * @param \Farpost\MapsBundle\Entity\Node $toNode
     * @return Edge
     */
    public function setToNode(\Farpost\MapsBundle\Entity\Node $toNode = null)
    {
        $this->toNode = $toNode;

        return $this;
    }

    /**
     * Get toNode
     *
     * @return \Farpost\MapsBundle\Entity\Node 
     */
    public function getToNode()
    {
        return $this->toNode;
    }

    /**
     * Add points
     *
     * @param \Farpost\MapsBundle\Entity\EdgePoint $points
     * @return Edge
     */
    public function addPoint(\Farpost\MapsBundle\Entity\EdgePoint $points)
    {
        $this->points[] = $points;

        return $this;
    }

    /**
     * Remove points
     *
     * @param \Farpost\MapsBundle\Entity\EdgePoint $points
     */
    public function removePoint(\Farpost\MapsBundle\Entity\EdgePoint $points)
    {
        $this->points->removeElement($points);
    }

    /**
     * Get points
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Set distance
     *
     * @param float $distance
     * @return Edge
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * Get distance
     *
     * @return float 
     */
    public function getDistance()
    {
        return $this->distance;
    }
}
