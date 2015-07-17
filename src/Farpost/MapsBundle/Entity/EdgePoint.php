<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 15/07/15
 * Time: 12:49
 */

namespace Farpost\MapsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class EdgePoint
 * @package Farpost\MapsBundle\Entity
 *
 * @ORM\Table(name="map_edge_points")
 * @ORM\Entity(repositoryClass="Farpost\MapsBundle\Entity\EdgePointRepository")
 */

class EdgePoint {

    static public $sqliteAnnotations = [
        'table' => 'path_segment_points',
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
                'name' => 'path_id',
                'type' => 'INTEGER',
                'PK' => false,
                'nullable' => false,
                'RK' => 'path_segments',
                'getter' => 'getEdge'
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
                'name' => 'seq',
                'type' => 'INTEGER',
                'PK' => false,
                'nullable' => false,
                'RK' => '',
                'getter' => 'getSeq'
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
     * @var Edge
     *
     * @ORM\ManyToOne(targetEntity="Edge", inversedBy="points")
     * @ORM\JoinColumn(name="edge_id", referencedColumnName="id", onDelete="CASCADE")
     * @ORM\OrderBy({"seq" = "asc"})
     */
    protected $edge;

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
     * @var integer
     *
     * @ORM\Column(name="seq", type="integer")
     */
    protected $seq;

    /**
     * Set id
     *
     * @param integer $id
     * @return EdgePoint
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
     * Set lon
     *
     * @param \float $lon
     * @return EdgePoint
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
     * Set lat
     *
     * @param \float $lat
     * @return EdgePoint
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
     * Set edge
     *
     * @param \Farpost\MapsBundle\Entity\Edge $edge
     * @return EdgePoint
     */
    public function setEdge(\Farpost\MapsBundle\Entity\Edge $edge = null)
    {
        $this->edge = $edge;

        return $this;
    }

    /**
     * Get edge
     *
     * @return \Farpost\MapsBundle\Entity\Edge 
     */
    public function getEdge()
    {
        return $this->edge;
    }

    /**
     * Set seq
     *
     * @param integer $seq
     * @return EdgePoint
     */
    public function setSeq($seq)
    {
        $this->seq = $seq;

        return $this;
    }

    /**
     * Get seq
     *
     * @return integer 
     */
    public function getSeq()
    {
        return $this->seq;
    }
}
