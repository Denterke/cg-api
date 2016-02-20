<?php

namespace Farpost\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PathSegments
 *
 * @ORM\Table(name="path_segments")
 * @ORM\Entity()
 */
class PathSegment
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
    * @var GeoObject
    *
    * @ORM\ManyToOne(targetEntity="GeoObject")
    * @ORM\JoinColumn(name="object_from_id", referencedColumnName="id")
    */
   protected $object_from;

   /**
    * @var GeoObject
    *
    * @ORM\ManyToOne(targetEntity="GeoObject")
    * @ORM\JoinColumn(name="object_to_id", referencedColumnName="id")
    */
   protected $object_to;


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
     * @return PathSegment
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
     * Set object_from
     *
     * @param \Farpost\StoreBundle\Entity\GeoObject $objectFrom
     * @return PathSegment
     */
    public function setObjectFrom(\Farpost\StoreBundle\Entity\GeoObject $objectFrom = null)
    {
        $this->object_from = $objectFrom;

        return $this;
    }

    /**
     * Get object_from
     *
     * @return \Farpost\StoreBundle\Entity\GeoObject 
     */
    public function getObjectFrom()
    {
        return $this->object_from;
    }

    /**
     * Set object_to
     *
     * @param \Farpost\StoreBundle\Entity\GeoObject $objectTo
     * @return PathSegment
     */
    public function setObjectTo(\Farpost\StoreBundle\Entity\GeoObject $objectTo = null)
    {
        $this->object_to = $objectTo;

        return $this;
    }

    /**
     * Get object_to
     *
     * @return \Farpost\StoreBundle\Entity\GeoObject 
     */
    public function getObjectTo()
    {
        return $this->object_to;
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return PathSegment
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
