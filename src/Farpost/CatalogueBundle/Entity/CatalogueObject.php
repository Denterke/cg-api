<?php

namespace Farpost\CatalogueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 *
 * @ORM\Table(name="catalogue_objects")
 * @ORM\Entity
 */

class CatalogueObject
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
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=50, nullable=true)
     */
    protected $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="site", type="string", length=100, nullable=true)
     */
    protected $site;

    /**
     * @ORM\OneToMany(targetEntity="CatalogueCategoryObjectEdge", mappedBy="object", cascade={"persist"}, orphanRemoval=true)
     */
    protected $categories;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return CatalogueObject
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
     * Set description
     *
     * @param string $description
     * @return CatalogueObject
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return CatalogueObject
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set site
     *
     * @param string $site
     * @return CatalogueObject
     */
    public function setSite($site)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site
     *
     * @return string 
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Add categories
     *
     * @param \Farpost\CatalogueBundle\Entity\CatalogueCategoryObjectEdge $edge
     * @return CatalogueObject
     */
    public function addCategory(\Farpost\CatalogueBundle\Entity\CatalogueCategoryObjectEdge $edge)
    {
        $edge->setObject($this);
        $this->categories[] = $edge;

        return $this;
    }

    /**
     * Remove categories
     *
     * @param \Farpost\CatalogueBundle\Entity\CatalogueCategoryObjectEdge $edge
     * @return CatalogueObject
     */
    public function removeCategory(\Farpost\CatalogueBundle\Entity\CatalogueCategoryObjectEdge $edge)
    {
        $edge->setObject(null);
        $this->categories->removeElement($edge);

        return $this;
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
