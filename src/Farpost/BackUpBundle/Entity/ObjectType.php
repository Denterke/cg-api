<?php

namespace Farpost\BackUpBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ObjectTypes
 *
 * @ORM\Table(name="catalog.object_types")
 * @ORM\Entity(repositoryClass="Farpost\BackUpBundle\Entity\ObjectTypeRepository")
 */
class ObjectType
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
     * @return ObjectType
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

    /**
     * Set id
     *
     * @param integer $id
     * @return ObjectType
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
