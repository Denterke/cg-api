<?php

namespace Farpost\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GeoObjects
 *
 * @ORM\Table(name="geoobjects")
 * @ORM\Entity(repositoryClass="Farpost\StoreBundle\Entity\GeoObjectRepository")
 */
class GeoObject
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
    * @ORM\Column(name="alias", type="string", length=255)
    */
   protected $alias;

   /**
    * @var GeoObjectType
    *
    * @ORM\ManyToOne(targetEntity="GeoObjectType")
    * @ORM\JoinColumn(name="geoobject_type_id", referencedColumnName="id")
    */
   protected $geoobject_type;

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
    * @var integer
    *
    * @ORM\Column(name="cataloged", type="integer")
    */
   protected $cataloged;

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
     * @return GeoObject
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
     * Set level
     *
     * @param integer $level
     * @return GeoObject
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
     * Set lan
     *
     * @param float $lan
     * @return GeoObject
     */
    public function setLan($lan)
    {
        $this->lan = $lan;

        return $this;
    }

    /**
     * Get lan
     *
     * @return float 
     */
    public function getLan()
    {
        return $this->lan;
    }

    /**
     * Set lat
     *
     * @param float $lat
     * @return GeoObject
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
     * Set cataloged
     *
     * @param integer $cataloged
     * @return GeoObject
     */
    public function setCataloged($cataloged)
    {
        $this->cataloged = $cataloged;

        return $this;
    }

    /**
     * Get cataloged
     *
     * @return integer 
     */
    public function getCataloged()
    {
        return $this->cataloged;
    }

    /**
     * Set geoobject_type
     *
     * @param \Farpost\StoreBundle\Entity\GeoObjectType $geoobjectType
     * @return GeoObject
     */
    public function setGeoobjectType(\Farpost\StoreBundle\Entity\GeoObjectType $geoobjectType = null)
    {
        $this->geoobject_type = $geoobjectType;

        return $this;
    }

    /**
     * Get geoobject_type
     *
     * @return \Farpost\StoreBundle\Entity\GeoObjectType 
     */
    public function getGeoobjectType()
    {
        return $this->geoobject_type;
    }

    /**
     * Set building
     *
     * @param \Farpost\StoreBundle\Entity\Building $building
     * @return GeoObject
     */
    public function setBuilding(\Farpost\StoreBundle\Entity\Building $building = null)
    {
        $this->building = $building;

        return $this;
    }

    /**
     * Get building
     *
     * @return \Farpost\StoreBundle\Entity\Building 
     */
    public function getBuilding()
    {
        return $this->building;
    }

    /**
     * Set lon
     *
     * @param float $lon
     * @return GeoObject
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
}
