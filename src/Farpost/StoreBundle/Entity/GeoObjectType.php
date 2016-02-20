<?php

namespace Farpost\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GeoObjectTypes
 *
 * @ORM\Table(name="geoobject_types")
 * @ORM\Entity(repositoryClass="Farpost\StoreBundle\Entity\GeoObjectTypeRepository")
 */
class GeoObjectType
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
    * @var integer
    *
    * @ORM\Column(name="displayed", type="integer")
    */
   protected $displayed;

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
     * @return GeoObjectType
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
     * Set displayed
     *
     * @param integer $displayed
     * @return GeoObjectType
     */
    public function setDisplayed($displayed)
    {
        $this->displayed = $displayed;

        return $this;
    }

    /**
     * Get displayed
     *
     * @return integer
     */
    public function getDisplayed()
    {
        return $this->displayed;
    }
}
