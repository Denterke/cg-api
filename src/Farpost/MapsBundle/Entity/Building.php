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
 * Buildings
 *
 * @ORM\Table(name="map_buildings")
 * @ORM\Entity(repositoryClass="Farpost\MapsBundle\Entity\BuildingRepository")
 */
class Building {

    static public $sqliteAnnotations = [
        'table'  => 'buildings',
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
                'name' => 'number',
                'type' => 'VARCHAR',
                'PK' => false,
                'nullable' => true,
                'RK' => '',
                'getter' => 'getNumber'
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
                'name' => 'name',
                'type' => 'VARCHAR',
                'PK' => false,
                'nullable' => true,
                'RK' => '',
                'getter' => 'getName'
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
                'name' => 'lat',
                'type' => 'DOUBLE',
                'PK' => false,
                'nullable' => true,
                'RK' => '',
                'getter' => 'getLat'
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
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=255)
     */
    protected $alias;

    /**
     * @var string
     *
     * @ORM\Column(name="number", type="string", length=255)
     */
    protected $number;

    /**
     * @var float
     *
     * @ORM\Column(name="lon", type="float", nullable=true)
     */
    protected $lon;

    /**
     * @var float
     *
     * @ORM\Column(name="lat", type="float", nullable=true)
     */
    protected $lat;

    /**
     * Set id
     *
     * @param integer $id
     * @return Building
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
     * Set alias
     *
     * @param string $alias
     * @return Building
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
     * Set number
     *
     * @param string $number
     * @return Building
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set lon
     *
     * @param float $lon
     * @return Building
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
     * @return Building
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

    public function getName()
    {
        if ($this->number === 'outdoor') {
            return 'Улица';
        } else {
            return "Корпус {$this->number}";
        }
    }
}
