<?php

namespace Farpost\CatalogueBundle\Entity;

use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 *
 * @ORM\Table(name="catalogue_objects")
 * @ORM\Entity(repositoryClass="Farpost\CatalogueBundle\Entity\CatalogueObjectRepository")
 */

class CatalogueObject
{

    static public $sqliteAnnotations = [
        'table' => 'objects',
        'virtual_table' => 'objects_search',
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
                'virtual' => true
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
                'name' => 'node_id',
                'type' => 'INTEGER',
                'PK' => false,
                'nullable' => true,
                'RK' => 'nodes',
                'getter' => 'getNode'
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
     * @var string
     *
     * @ORM\Column(name="site", type="string", length=100, nullable=true)
     */
    protected $site;

    /**
     * @ORM\OneToMany(targetEntity="CatalogueCategoryObjectEdge", mappedBy="object", cascade={"persist"}, orphanRemoval=true)
     */
    protected $categories;

    /**
     * @ORM\OneToMany(targetEntity="CatalogueObjectSchedule", mappedBy="object", cascade={"persist"}, orphanRemoval=true)
     */
    protected $schedule;

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
     * @var Farpost\MapsBundle\Entity\Node
     *
     * @ORM\ManyToOne(targetEntity="Farpost\MapsBundle\Entity\Node", inversedBy="objects")
     * @ORM\JoinColumn(name="node_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    protected $node;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="CatalogueObjectMedia", mappedBy="object", cascade={"all"}, orphanRemoval=true)
     */
    protected $images;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->schedule = new ArrayCollection();
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

    /**
     * Add schedule
     *
     * @param \Farpost\CatalogueBundle\Entity\CatalogueObjectSchedule $schedule
     * @return CatalogueObject
     */
    public function addSchedule(\Farpost\CatalogueBundle\Entity\CatalogueObjectSchedule $schedule)
    {
        $schedule->setObject($this);
        $this->schedule[] = $schedule;

        return $this;
    }

    /**
     * Remove schedule
     *
     * @param \Farpost\CatalogueBundle\Entity\CatalogueObjectSchedule $schedule
     * @return CatalogueObject
     */
    public function removeSchedule(\Farpost\CatalogueBundle\Entity\CatalogueObjectSchedule $schedule)
    {
        $schedule->setObject(null);
        $this->schedule->removeElement($schedule);

        return $this;
    }

    /**
     * Get schedule
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSchedule()
    {
        return $this->schedule;
    }

    /**
     * Set logoStandard
     *
     * @param \Farpost\CatalogueBundle\Entity\CatalogueImage $logoStandard
     * @return CatalogueObject
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
     * @return CatalogueObject
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
     * Set node
     *
     * @param \Farpost\MapsBundle\Entity\Node $node
     * @return CatalogueObject
     */
    public function setNode(\Farpost\MapsBundle\Entity\Node $node = null)
    {
        $this->node = $node;

        return $this;
    }

    /**
     * Get node
     *
     * @return \Farpost\MapsBundle\Entity\Node 
     */
    public function getNode()
    {
        return $this->node;
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
     * Add images
     *
     * @param \Farpost\CatalogueBundle\Entity\CatalogueObjectMedia $image
     * @return CatalogueObject
     */
    public function addImage(\Farpost\CatalogueBundle\Entity\CatalogueObjectMedia $image)
    {
        $image->setObject($this);
        $this->images[] = $image;

        return $this;
    }

    /**
     * Remove images
     *
     * @param \Farpost\CatalogueBundle\Entity\CatalogueMedia $image
     */
    public function removeImage(\Farpost\CatalogueBundle\Entity\CatalogueObjectMedia $image)
    {
        $image->setObject($this);
        $this->images->removeElement($image);

        return $this;
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getImages()
    {
        return $this->images;
    }
}
