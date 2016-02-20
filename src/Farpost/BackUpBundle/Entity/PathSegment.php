<?php

namespace Farpost\BackUpBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PathSegments
 *
 * @ORM\Table(name="catalog.path_segments")
 * @ORM\Entity(repositoryClass="Farpost\BackUpBundle\Entity\PathSegmentRepository")
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
    * @var Object
    *
    * @ORM\ManyToOne(targetEntity="Object")
    * @ORM\JoinColumn(name="id_vertex_from", referencedColumnName="id")
    */
   protected $object_from;

   /**
    * @var Object
    *
    * @ORM\ManyToOne(targetEntity="Object")
    * @ORM\JoinColumn(name="id_vertex_to", referencedColumnName="id")
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
     * @param \Farpost\BackUpBundle\Entity\Object $objectFrom
     * @return PathSegment
     */
    public function setObjectFrom(\Farpost\BackUpBundle\Entity\Object $objectFrom = null)
    {
        $this->object_from = $objectFrom;

        return $this;
    }

    /**
     * Get object_from
     *
     * @return \Farpost\BackUpBundle\Entity\Object
     */
    public function getObjectFrom()
    {
        return $this->object_from;
    }

    /**
     * Set object_to
     *
     * @param \Farpost\BackUpBundle\Entity\Object $objectTo
     * @return PathSegment
     */
    public function setObjectTo(\Farpost\BackUpBundle\Entity\Object $objectTo = null)
    {
        $this->object_to = $objectTo;

        return $this;
    }

    /**
     * Get object_to
     *
     * @return \Farpost\BackUpBundle\Entity\Object
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
