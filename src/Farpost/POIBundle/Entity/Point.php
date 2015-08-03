<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 31/07/15
 * Time: 12:38
 */

namespace Farpost\POIBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Farpost\MapsBundle\Entity\Level;
use Farpost\MapsBundle\Entity\Node;

/**
 * Class Point
 * @package Farpost\POIBundle\Entity
 *
 * @ORM\Table(name="poi_points")
 * @ORM\Entity(repositoryClass="Farpost\POIBundle\Entity\PointRepository")
 */
class Point
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255)
     */
    protected $label;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="string")
     */
    protected $content;

    /**
     * @var float
     *
     * @ORM\Column(name="lat", type="float", nullable=true)
     */
    protected $lat;

    /**
     * @var float
     *
     * @ORM\Column(name="lon", type="float", nullable=true)
     */
    protected $lon;

    /**
     * @var Level
     *
     * @ORM\ManyToOne(targetEntity="\Farpost\MapsBundle\Entity\Level")
     * @ORM\JoinColumn(name="level_id", referencedColumnName="id")
     */
    protected $level;

    /**
     * @var Node
     *
     * @ORM\ManyToOne(targetEntity="\Farpost\MapsBundle\Entity\Node")
     * @ORM\JoinColumn(name="node_id", referencedColumnName="id")
     */
    protected $node;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean")
     */
    protected $visible;

    /**
     * @var Type
     *
     * @ORM\ManyToOne(targetEntity="Type", inversedBy="points")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     */
    protected $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_at", type="datetime")
     */
    protected $startAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_at", type="datetime")
     */
    protected $endAt;

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
     * Set label
     *
     * @param string $label
     * @return Point
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Point
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set lat
     *
     * @param float $lat
     * @return Point
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat
     *
     * @return float 
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lon
     *
     * @param float $lon
     * @return Point
     */
    public function setLon($lon)
    {
        $this->lon = $lon;

        return $this;
    }

    /**
     * Get lon
     *
     * @return float 
     */
    public function getLon()
    {
        return $this->lon;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     * @return Point
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean 
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set type
     *
     * @param \Farpost\POIBundle\Entity\Type $type
     * @return Point
     */
    public function setType(\Farpost\POIBundle\Entity\Type $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \Farpost\POIBundle\Entity\Type 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set level
     *
     * @param \Farpost\MapsBundle\Entity\Level $level
     * @return Point
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
     * Set node
     *
     * @param \Farpost\MapsBundle\Entity\Node $node
     * @return Point
     */
    public function setNode(\Farpost\MapsBundle\Entity\Node $node = null)
    {
        $this->node = $node;

        return $this;
    }

    /**
     * Get node
     *
     * @return \Farpost\MapsBundle\Entity\Node 
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * Set startAt
     *
     * @param \DateTime $startAt
     * @return Point
     */
    public function setStartAt($startAt)
    {
        $this->startAt = $startAt;

        return $this;
    }

    /**
     * Get startAt
     *
     * @return \DateTime 
     */
    public function getStartAt()
    {
        return $this->startAt;
    }

    /**
     * Set endAt
     *
     * @param \DateTime $endAt
     * @return Point
     */
    public function setEndAt($endAt)
    {
        $this->endAt = $endAt;

        return $this;
    }

    /**
     * Get endAt
     *
     * @return \DateTime 
     */
    public function getEndAt()
    {
        return $this->endAt;
    }

    /**
     * @return float
     */
    public function getRealLat()
    {
        return $this->node
            ? $this->node->getLat()
            : $this->lat
        ;
    }

    /**
     * @return float
     */
    public function getRealLon()
    {
        return $this->node
            ? $this->node->getLon()
            : $this->lon
        ;
    }

    /**
     * @return integer
     */
    public function getRealLevel()
    {
        return $this->getNode()
            ? $this->getNode()->getLevel()->getLevel()
            : $this->getLevel()->getLevel()
        ;
    }
}
