<?php

namespace Farpost\CatalogueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 *
 * @ORM\Table(name="catalogue_categories")
 * @ORM\Entity
 */

class CatalogueCategory
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
     * @var boolean
     *
     * @ORM\Column(name="is_organization", type="boolean", nullable=true)
     */
    protected $isOrganization;

    /**
     * @var CatalogueCategoryEdge
     *
     * @ORM\OneToMany(targetEntity="CatalogueCategoryEdge", mappedBy="child", cascade={"persist"}, orphanRemoval=true)
     */
    protected $parents;

    /**
     * @var CatalogueCategoryEdge
     *
     * @ORM\OneToMany(targetEntity="CatalogueCategoryEdge", mappedBy="parent", cascade={"persist"}, orphanRemoval=true)
     */
    protected $children;

    /**
     * @var string
     *
     * @ORM\Column(name="site", type="string", length=100, nullable=true)
     */
    protected $site;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->parents = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    public function getSite()
    {
        return $this->site;
    }

    public function setSite($site)
    {
        $this->site = $site;

        return $this->site;
    }

    /**
     * Set isOrganization
     *
     * @param boolean $isOrganization
     * @return CatalogueCategory
     */
    public function setIsOrganization($isOrganization)
    {
        $this->isOrganization = $isOrganization;

        return $this;
    }

    /**
     * Get isOrganization
     *
     * @return boolean 
     */
    public function getIsOrganization()
    {
        return $this->isOrganization;
    }

    /**
     * Add parents
     *
     * @param \Farpost\CatalogueBundle\Entity\CatalogueCategoryEdge $parents
     * @return CatalogueCategory
     */
    public function addParent(\Farpost\CatalogueBundle\Entity\CatalogueCategoryEdge $parents)
    {
        $this->parents[] = $parents;

        return $this;
    }

    /**
     * Remove parents
     *
     * @param \Farpost\CatalogueBundle\Entity\CatalogueCategoryEdge $parents
     * @return CatalogueCategory
     */
    public function removeParent(\Farpost\CatalogueBundle\Entity\CatalogueCategoryEdge $parents)
    {
        $this->parents->removeElement($parents);

        return $this;
    }

    /**
     * Get parents
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getParents()
    {
        return $this->parents;
    }

    /**
     * Add children
     *
     * @param \Farpost\CatalogueBundle\Entity\CatalogueCategoryEdge $linkWithChild
     * @return CatalogueCategory
     */
    public function addChild(\Farpost\CatalogueBundle\Entity\CatalogueCategoryEdge $linkWithChild)
    {
        $linkWithChild->setParent($this);
        $this->children[] = $linkWithChild;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Farpost\CatalogueBundle\Entity\CatalogueCategoryEdge $linkWithChild
     * @return CatalogueCategory
     */
    public function removeChild(\Farpost\CatalogueBundle\Entity\CatalogueCategoryEdge $linkWithChild)
    {
        $linkWithChild->setParent(null);
        $this->children->removeElement($linkWithChild);

        return $this;
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
