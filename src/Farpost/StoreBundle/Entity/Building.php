<?php

namespace Farpost\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Buildings
 *
 * @ORM\Table(name="buildings")
 * @ORM\Entity(repositoryClass="Farpost\StoreBundle\Entity\BuildingRepository")
 */
class Building
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

   public function __construct()
   {
      $this->building_types = new ArrayCollection();
   }

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
}
