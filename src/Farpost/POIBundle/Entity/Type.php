<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 31/07/15
 * Time: 12:26
 */

namespace Farpost\POIBundle\Entity;

use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Type
 * @package Farpost\POIBundle\Entity
 *
 * @ORM\Table(name="poi_types")
 * @ORM\Entity
 */
class Type
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=255, unique=true)
     */
    protected $alias;

    /**
     * @var Media
     *
     * @ORM\OneToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"all"})
     * @ORM\JoinColumn(name="icon_id", referencedColumnName="id")
     */
    protected $icon;

    /**
     * @var Group
     *
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="types")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     */
    protected $group;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Point", mappedBy="type")
     */
    protected $points;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean")
     */
    protected $visible;


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
     * Set name
     *
     * @param string $name
     * @return Type
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set alias
     *
     * @param string $alias
     * @return Type
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
     * Set visible
     *
     * @param boolean $visible
     * @return Type
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean 
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set icon
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $icon
     * @return Type
     */
    public function setIcon(\Application\Sonata\MediaBundle\Entity\Media $icon = null)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get icon
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media 
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Set group
     *
     * @param \Farpost\POIBundle\Entity\Group $group
     * @return Type
     */
    public function setGroup(\Farpost\POIBundle\Entity\Group $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \Farpost\POIBundle\Entity\Group 
     */
    public function getGroup()
    {
        return $this->group;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->points = new \Doctrine\Common\Collections\ArrayCollection();
        $this->visible = true;
    }

    /**
     * Add points
     *
     * @param \Farpost\POIBundle\Entity\Point $points
     * @return Type
     */
    public function addPoint(\Farpost\POIBundle\Entity\Point $points)
    {
        $this->points[] = $points;

        return $this;
    }

    /**
     * Remove points
     *
     * @param \Farpost\POIBundle\Entity\Point $points
     */
    public function removePoint(\Farpost\POIBundle\Entity\Point $points)
    {
        $this->points->removeElement($points);
    }

    /**
     * Get points
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Stringifies entity
     *
     * @return string
     */
    public function __toString()
    {
        return join(' - ', [$this->name, $this->alias]);
    }
}
