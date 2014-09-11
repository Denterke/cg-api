<?php

namespace Farpost\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PathSegmentPoint
 *
 * @ORM\Table(name="path_segment_points")
 * @ORM\Entity()
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
    * @var integer
    *
    * @ORM\Column(name="level", type="integer")
    */
   protected $level;

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
     * Set level
     *
     * @param integer $level
     * @return PathSegmentPoint
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
     * @param \Farpost\StoreBundle\Entity\PathSegment $pathSegment
     * @return PathSegmentPoint
     */
    public function setPathSegment(\Farpost\StoreBundle\Entity\PathSegment $pathSegment = null)
    {
        $this->pathSegment = $pathSegment;

        return $this;
    }

    /**
     * Get pathSegment
     *
     * @return \Farpost\StoreBundle\Entity\PathSegment 
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
