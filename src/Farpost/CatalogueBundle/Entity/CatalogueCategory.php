<?php

namespace Farpost\CatalogueBundle\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 *
 * @ORM\Table(name="catalogue_categories")
 * @ORM\Entity
 */

class CatalogueCategory
{

    static public $sqliteAnnotations = [
        'table' => 'categories',
        'virtual_table' => 'categories_search',
        'fields' => [
            [
                'name' => '_id',
                'type' => 'INTEGER',
                'PK' => true,
                'nullable' => false,
                'RK' => '',
                'getter' => 'getId',
                'virtual' => true
            ],
            [
                'name' => 'name',
                'type' => 'VARCHAR',
                'PK' => false,
                'nullable' => false,
                'RK' => '',
                'getter' => 'getName',
                'virtual' => 'true'
            ],
            [
                'name' => 'is_organization',
                'type' => 'BOOLEAN',
                'PK' => false,
                'nullable' => false,
                'RK' => '',
                'getter' => 'getIsOrganization'
            ],
            [
                'name' => 'description',
                'type' => 'VARCHAR',
                'PK' => false,
                'nullable' => true,
                'RK' => '',
                'getter' => 'getDescription',
                'virtual' => true
            ],
            [
                'name' => 'logo_standard',
                'type' => 'VARCHAR',
                'PK' => false,
                'nullable' => true,
                'RK' => '',
                'getter' => 'getLogoStandardUrl'
            ],
            [
                'name' => 'logo_thumbnail',
                'type' => 'VARCHAR',
                'PK' => false,
                'nullable' => true,
                'RK' => '',
                'getter' => 'getLogoThumbnailUrl'
            ],
            [
                'name' => 'phone',
                'type' => 'VARCHAR',
                'PK' => false,
                'nullable' => true,
                'RK' => '',
                'getter' => 'getPhone'
            ],
            [
                'name' => 'site',
                'type' => 'VARCHAR',
                'PK' => false,
                'nullable' => true,
                'RK' => '',
                'getter' => 'getSite'
            ],
            [
                'name' => 'is_root',
                'type' => 'BOOLEAN',
                'PK' => false,
                'nullable' => false,
                'RK' => '',
                'getter' => 'getIsRoot'
            ]
        ]
    ];

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
     * @var CatalogueCategoryObjectEdge
     *
     * @ORM\OneToMany(targetEntity="CatalogueCategoryObjectEdge", mappedBy="category", cascade={"persist"}, orphanRemoval=true)
     */
    protected $objects;

    /**
     * @var string
     *
     * @ORM\Column(name="site", type="string", length=100, nullable=true)
     */
    protected $site;

    /**
     * @ORM\OneToOne(targetEntity="CatalogueImage", cascade={"persist"}, orphanRemoval=true)
     * @ORM\JoinColumn(name="logo_standard_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $logoStandard;

    /**
     * @ORM\OneToOne(targetEntity="CatalogueImage", cascade={"persist"}, orphanRemoval=true)
     * @ORM\JoinColumn(name="logo_thumbnail_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $logoThumbnail;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_root", type="boolean", nullable=false)
     */
    protected $isRoot;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->parents = new ArrayCollection();
        $this->objects = new ArrayCollection();
        $this->isRoot = false;
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
     * @param \Farpost\CatalogueBundle\Entity\CatalogueCategoryEdge $edge
     * @return CatalogueCategory
     */
    public function addChild(\Farpost\CatalogueBundle\Entity\CatalogueCategoryEdge $edge)
    {
        $edge->setParent($this);
        $this->children[] = $edge;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Farpost\CatalogueBundle\Entity\CatalogueCategoryEdge $edge
     * @return CatalogueCategory
     */
    public function removeChild(\Farpost\CatalogueBundle\Entity\CatalogueCategoryEdge $edge)
    {
        $edge->setParent(null);
        $this->children->removeElement($edge);

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

    /**
     * Add objects
     *
     * @param \Farpost\CatalogueBundle\Entity\CatalogueCategoryObjectEdge $edge
     * @return CatalogueCategory
     */
    public function addObject(\Farpost\CatalogueBundle\Entity\CatalogueCategoryObjectEdge $edge)
    {
        $edge->setCategory($this);
        $this->objects[] = $edge;

        return $this;
    }

    /**
     * Remove objects
     *
     * @param \Farpost\CatalogueBundle\Entity\CatalogueCategoryObjectEdge $edge
     * @return CatalogueCategory
     */
    public function removeObject(\Farpost\CatalogueBundle\Entity\CatalogueCategoryObjectEdge $edge)
    {
        $edge->setCategory(null);
        $this->objects->removeElement($edge);

        return $this;
    }

    /**
     * Get objects
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getObjects()
    {
        return $this->objects;
    }

    /**
     * Set logoStandard
     *
     * @param \Farpost\CatalogueBundle\Entity\CatalogueImage $logoStandard
     * @return CatalogueCategory
     */
    public function setLogoStandard(\Farpost\CatalogueBundle\Entity\CatalogueImage $logoStandard = null)
    {
        $this->logoStandard = $logoStandard;

        return $this;
    }

    /**
     * Get logoStandard
     *
     * @return \Farpost\CatalogueBundle\Entity\CatalogueImage 
     */
    public function getLogoStandard()
    {
        return $this->logoStandard;
    }

    /**
     * Set logoThumbnail
     *
     * @param \Farpost\CatalogueBundle\Entity\CatalogueImage $logoThumbnail
     * @return CatalogueCategory
     */
    public function setLogoThumbnail(\Farpost\CatalogueBundle\Entity\CatalogueImage $logoThumbnail = null)
    {
        $this->logoThumbnail = $logoThumbnail;

        return $this;
    }

    /**
     * Get logoThumbnail
     *
     * @return \Farpost\CatalogueBundle\Entity\CatalogueImage 
     */
    public function getLogoThumbnail()
    {
        return $this->logoThumbnail;
    }

    /**
     * @return string
     */
    public function getLogoStandardUrl()
    {
        return $this->logoStandard
            ? $this->logoStandard->getWebPath()
            : null;
    }

    /**
     * @return string
     */
    public function getLogoThumbnailUrl()
    {
        return $this->logoThumbnail
            ? $this->logoThumbnail->getWebPath()
            : null;
    }

    /**
     * Set isRoot
     *
     * @param boolean $isRoot
     * @return CatalogueCategory
     */
    public function setIsRoot($isRoot)
    {
        $this->isRoot = $isRoot;

        return $this;
    }

    /**
     * Get isRoot
     *
     * @return boolean 
     */
    public function getIsRoot()
    {
        return $this->isRoot;
    }
}
