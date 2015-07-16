<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 15/07/15
 * Time: 12:48
 */

namespace Farpost\MapsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * Edges
 *
 * @ORM\Table(name="map_edges")
 * @ORM\Entity(repositoryClass="Farpost\MapsBundle\Entity\EdgeRepository")
 */
class Edge {
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
}
