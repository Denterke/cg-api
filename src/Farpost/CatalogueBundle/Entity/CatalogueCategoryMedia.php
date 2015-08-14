<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 30/07/15
 * Time: 13:34
 */

namespace Farpost\CatalogueBundle\Entity;

use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="catalogue_category_media")
 */
class CatalogueCategoryMedia
{
    static public $sqliteAnnotations = [
        'table' => 'categories_images',
        'fields' => [
            [
                'name' => '_id',
                'type' => 'INTEGER',
                'PK' => true,
                'nullable' => false,
                'RK' => '',
                'getter' => 'getId',
            ],
            [
                'name' => 'object_id',
                'type' => 'INTEGER',
                'PK' => false,
                'nullable' => false,
                'RK' => 'categories',
                'getter' => 'getCategory',
            ],
            [
                'name' => 'standard_url',
                'type' => 'VARCHAR',
                'PK' => false,
                'nullable' => true,
                'RK' => '',
                'getter' => 'getStandardUrl',
                'injections' => [
                    'sonata.media.provider.image'
                ],
            ],
            [
                'name' => 'thumbnail_url',
                'type' => 'VARCHAR',
                'PK' => false,
                'nullable' => true,
                'RK' => '',
                'getter' => 'getThumbnailUrl',
                'injections' => [
                    'sonata.media.provider.image'
                ]
            ],
            [
                'name' => 'width',
                'type' => 'INTEGER',
                'PK' => false,
                'nullable' => true,
                'RK' => '',
                'injections' => [
                    'sonata.media.provider.image'
                ],
                'getter' => 'getWidth'
            ],
            [
                'name' => 'height',
                'type' => 'INTEGER',
                'PK' => false,
                'nullable' => true,
                'RK' => '',
                'injections' => [
                    'sonata.media.provider.image'
                ],
                'getter' => 'getHeight'
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
     * @var CatalogueObject
     *
     * @ORM\ManyToOne(targetEntity="CatalogueCategory", inversedBy="images")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    protected $category;

    /**
     * @var Media
     *
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"all"})
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id")
     */
    protected $media;

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
     * Set category
     *
     * @param \Farpost\CatalogueBundle\Entity\CatalogueCategory $category
     * @return CatalogueCategoryMedia
     */
    public function setCategory(\Farpost\CatalogueBundle\Entity\CatalogueCategory $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Farpost\CatalogueBundle\Entity\CatalogueCategory 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set media
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $media
     * @return CatalogueCategoryMedia
     */
    public function setMedia(\Application\Sonata\MediaBundle\Entity\Media $media = null)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Get media
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media 
     */
    public function getMedia()
    {
        return $this->media;
    }

    public function getThumbnailUrl($injections)
    {
        $imageProvider = $injections[0];
        if (!$this->getMedia()) {
            return null;
        }
        $format = $imageProvider->getFormatName($this->media, 'thumbnail');

        return '/' . $imageProvider->generatePublicUrl($this->media, $format);
    }

    public function getStandardUrl($injections)
    {
        $imageProvider = $injections[0];
        if (!$this->getMedia()) {
            return null;
        }
        $format = $imageProvider->getFormatName($this->media, 'reference');

        return '/' . $imageProvider->generatePublicUrl($this->media, $format);
    }

    public function getWidth($injections)
    {
        $imageProvider = $injections[0];
        if (!$this->getMedia()) {
            return null;
        }
        $properties = $imageProvider->getHelperProperties($this->getMedia(), 'reference');

        return $properties['width'];
    }

    public function getHeight($injections)
    {
        $imageProvider = $injections[0];
        if (!$this->getMedia()) {
            return null;
        }
        $properties = $imageProvider->getHelperProperties($this->getMedia(), 'reference');

        return $properties['height'];
    }
}
