<?php

namespace Farpost\BackUpBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PathSegmentPoint
 *
 * @ORM\Table(name="catalog.path_segment_points")
 * @ORM\Entity(repositoryClass="Farpost\BackUpBundle\Entity\PathSegmentPointRepository")
 */
class PathSegmentPoint
{
   /**
    * @var integer
    *
    * @ORM\Column(name="id", type="integer")
    * @ORM\Id
    */
   protected $id;

   /**
    * @var PathSegment
    *
    * @ORM\ManyToOne(targetEntity="PathSegment")
    * @ORM\JoinColumn(name="path_id", referencedColumnName="id")
    */
   protected $pathSegment;

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
    * @ORM\Column(name="idx", type="integer")
    */
   protected $idx;

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
     * @param float $lon
     * @return PathSegmentPoint
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
     * @return PathSegmentPoint
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
     * Set idx
     *
     * @param integer $idx
     * @return PathSegmentPoint
     */
    public function setIdx($idx)
    {
        $this->idx = $idx;

        return $this;
    }

    /**
     * Get idx
     *
     * @return integer
     */
    public function getIdx()
    {
        return $this->idx;
    }

    /**
     * Set pathSegment
     *
     * @param \Farpost\BackUpBundle\Entity\PathSegment $pathSegment
     * @return PathSegmentPoint
     */
    public function setPathSegment(\Farpost\BackUpBundle\Entity\PathSegment $pathSegment = null)
    {
        $this->pathSegment = $pathSegment;

        return $this;
    }

    /**
     * Get pathSegment
     *
     * @return \Farpost\BackUpBundle\Entity\PathSegment
     */
    public function getPathSegment()
    {
        return $this->pathSegment;
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return PathSegmentPoint
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
