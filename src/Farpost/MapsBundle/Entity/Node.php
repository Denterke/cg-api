<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 15/07/15
 * Time: 12:47
 */

namespace Farpost\MapsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Node
 * @package Farpost\MapsBundle\Entity
 *
 * @ORM\Table(name="map_nodes")
 * @ORM\Entity(repositoryClass="Farpost\MapsBundle\Entity\NodeRepository")
 */

class Node {

    static public $sqliteAnnotations = [
        'table'  => 'nodes',
        'fields' => [
            [
                'name' =>'_id',
                'type' => 'INTEGER',
                'PK' => true,
                'RK' => '',
                'nullable' => false,
                'getter' => 'getId'
            ],
            [
                'name' => 'building_id',
                'type' => 'INTEGER',
                'PK' => false,
                'RK' => 'buildings',
                'nullable' => false,
                'getter' => 'getBuilding'
            ],
            [
                'name' => 'level_id',
                'type' => 'INTEGER',
                'PK' => false,
                'RK' => 'levels',
                'nullable' => 'false',
                'getter' => 'getLevel'
            ],
            [
                'name' => 'alias',
                'type' => 'VARCHAR',
                'PK' => false,
                'nullable' => true,
                'RK' => '',
                'getter' => 'getAlias'
            ],
            [
                'name' => 'alias_lowercase',
                'type' => 'VARCHAR',
                'PK' => false,
                'nullable' => true,
                'RK' => '',
                'getter' => 'getLowerAlias'
            ],
            [
                'name' => 'lat',
                'type' => 'DOUBLE',
                'PK' => false,
                'nullable' => true,
                'RK' => '',
                'getter' => 'getLat'
            ],
            [
                'name' => 'lon',
                'type' => 'DOUBLE',
                'PK' => false,
                'nullable' => true,
                'RK' => '',
                'getter' => 'getLon'
            ],
            [
                'name' => 'type_id',
                'type' => 'INTEGER',
                'PK' => false,
                'nullable' => true,
                'RK' => 'node_types',
                'getter' => 'getType'
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
     * @var Building
     * @ORM\ManyToOne(targetEntity="Building")
     * @ORM\JoinColumn(name="building_id", referencedColumnName="id", nullable=true)
     */
    protected $building;

    /**
     * @var Level
     *
     * @ORM\ManyToOne(targetEntity="Level")
     * @ORM\JoinColumn(name="level_id", referencedColumnName="id", nullable=true)
     */
    protected $level;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=255, nullable=true)
     */
    protected $alias;

    /**
     * @var NodeType
     *
     * @ORM\ManyToOne(targetEntity="NodeType")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id", nullable=true)
     */
    protected $type;

    /**
     * @var float
     *
     * @ORM\Column(name="lat", type="float", nullable=false)
     */
    protected $lat;

    /**
     * @var float
     *
     * @ORM\Column(name="lon", type="float", nullable=false)
     */
    protected $lon;

    /**
     * @ORM\OneToMany(targetEntity="Farpost\CatalogueBundle\Entity\CatalogueObject", mappedBy="node")
     */
    protected $objects;


    public function __construct() {
        $this->objects = new ArrayCollection();
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
     * Set id
     *
     * @param integer $id
     * @return Node
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set alias
     *
     * @param string $alias
     * @return Node
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
     * Set lat
     *
     * @param \float $lat
     * @return Node
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat
     *
     * @return \float
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lon
     *
     * @param \float $lon
     * @return Node
     */
    public function setLon($lon)
    {
        $this->lon = $lon;

        return $this;
    }

    /**
     * Get lon
     *
     * @return \float
     */
    public function getLon()
    {
        return $this->lon;
    }

    /**
     * Set building
     *
     * @param \Farpost\MapsBundle\Entity\Building $building
     * @return Node
     */
    public function setBuilding(\Farpost\MapsBundle\Entity\Building $building = null)
    {
        $this->building = $building;

        return $this;
    }

    /**
     * Get building
     *
     * @return \Farpost\MapsBundle\Entity\Building 
     */
    public function getBuilding()
    {
        return $this->building;
    }

    /**
     * Set level
     *
     * @param \Farpost\MapsBundle\Entity\Level $level
     * @return Node
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
     * Set type
     *
     * @param \Farpost\MapsBundle\Entity\NodeType $type
     * @return Node
     */
    public function setType(\Farpost\MapsBundle\Entity\NodeType $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \Farpost\MapsBundle\Entity\NodeType 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Add objects
     *
     * @param \Farpost\CatalogueBundle\Entity\CatalogueObject $objects
     * @return Node
     */
    public function addObject(\Farpost\CatalogueBundle\Entity\CatalogueObject $objects)
    {
        $this->objects[] = $objects;

        return $this;
    }

    /**
     * Remove objects
     *
     * @param \Farpost\CatalogueBundle\Entity\CatalogueObject $objects
     */
    public function removeObject(\Farpost\CatalogueBundle\Entity\CatalogueObject $objects)
    {
        $this->objects->removeElement($objects);
    }

    /**
     * Get objects
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getObjects()
    {
        return $this->objects;
    }

    /**
     * Stringifies entity
     *
     * @return string
     */
    public function __toString()
    {
        return join(' - ', [$this->alias, $this->getType()->getAlias()]);
    }

    /**
     * Return alias in lowercase
     *
     * @return string
     */
    public function getLowerAlias()
    {
        return mb_strtolower($this->alias, 'UTF-8');
    }
}
