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
use Sonata\MediaBundle\Provider\ImageProvider;

/**
 * @ORM\Entity
 * @ORM\Table(name="catalogue_object_media")
 */
class CatalogueObjectMedia
{
    static public $sqliteAnnotations = [
        'table' => 'objects_images',
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
                'RK' => 'objects',
                'getter' => 'getObject',
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
     * @ORM\ManyToOne(targetEntity="CatalogueObject", inversedBy="images")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id")
     */
    protected $object;

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
     * Set object
     *
     * @param \Farpost\CatalogueBundle\Entity\CatalogueObject $object
     * @return CatalogueMedia
     */
    public function setObject(\Farpost\CatalogueBundle\Entity\CatalogueObject $object = null)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * Get object
     *
     * @return \Farpost\CatalogueBundle\Entity\CatalogueObject 
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Set media
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $media
     * @return CatalogueMedia
     */
    public function setMedia(\Application\Sonata\MediaBundle\Entity\Media $media = null)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Get media
     *
     * @return \Farpost\CatalogueBundle\Entity\Media 
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
        return $imageProvider->generatePublicUrl($this->media, $format);
    }

    public function getStandardUrl($injections)
    {
        $imageProvider = $injections[0];
        if (!$this->getMedia()) {
            return null;
        }
        $format = $imageProvider->getFormatName($this->media, 'reference');
        return $imageProvider->generatePublicUrl($this->media, $format);
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
