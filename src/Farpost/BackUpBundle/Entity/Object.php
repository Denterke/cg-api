<?php

namespace Farpost\BackUpBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GeoObjects
 *
 * @ORM\Table(name="catalog.objects")
 * @ORM\Entity(repositoryClass="Farpost\BackUpBundle\Entity\ObjectRepository")
 */
class Object
{
   /**
    * @var integer
    *
    * @ORM\Column(name="id", type="integer")
    * @ORM\Id
    */
   protected $id;

   /**
    * @var string
    *
    * @ORM\Column(name="alias", type="string", length=255)
    */
   protected $alias;

   /**
    * @var GeoObjectType
    *
    * @ORM\ManyToOne(targetEntity="ObjectType")
    * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
    */
   protected $object_type;

   /**
    * @var NodeType
    *
    * @ORM\ManyToOne(targetEntity="NodeType")
    * @ORM\JoinColumn(name="node_id", referencedColumnName="id")
    */
   protected $node_type;

   /**
    * @var integer
    *
    * @ORM\Column(name="status", type="integer")
    */
   protected $status;

   /**
    * @var Building
    *
    * @ORM\ManyToOne(targetEntity="Building")
    * @ORM\JoinColumn(name="building_id", referencedColumnName="id")
    */
   protected $building;

   /**
    * @var integer
    *
    * @ORM\Column(name="level", type="integer")
    */
   protected $level;

   /**
    * @var float
    *
    * @ORM\Column(name="lon", type="float")
    */
   protected $lon;

   /**
    * @var float
    *
    * @ORM\Column(name="lat", type="float")
    */
   protected $lat;

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
     * Set alias
     *
     * @param string $alias
     * @return Object
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias
     *
     * @return string 
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Object
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set level
     *
     * @param integer $level
     * @return Object
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return integer 
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set lon
     *
     * @param float $lon
     * @return Object
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
     * Set lat
     *
     * @param float $lat
     * @return Object
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
     * Set object_type
     *
     * @param \Farpost\BackUpBundle\Entity\ObjectType $objectType
     * @return Object
     */
    public function setObjectType(\Farpost\BackUpBundle\Entity\ObjectType $objectType = null)
    {
        $this->object_type = $objectType;

        return $this;
    }

    /**
     * Get object_type
     *
     * @return \Farpost\BackUpBundle\Entity\ObjectType 
     */
    public function getObjectType()
    {
        return $this->object_type;
    }

    /**
     * Set node_type
     *
     * @param \Farpost\BackUpBundle\Entity\NodeType $nodeType
     * @return Object
     */
    public function setNodeType(\Farpost\BackUpBundle\Entity\NodeType $nodeType = null)
    {
        $this->node_type = $nodeType;

        return $this;
    }

    /**
     * Get node_type
     *
     * @return \Farpost\BackUpBundle\Entity\NodeType 
     */
    public function getNodeType()
    {
        return $this->node_type;
    }

    /**
     * Set building
     *
     * @param \Farpost\BackUpBundle\Entity\Building $building
     * @return Object
     */
    public function setBuilding(\Farpost\BackUpBundle\Entity\Building $building = null)
    {
        $this->building = $building;

        return $this;
    }

    /**
     * Get building
     *
     * @return \Farpost\BackUpBundle\Entity\Building 
     */
    public function getBuilding()
    {
        return $this->building;
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return Object
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
