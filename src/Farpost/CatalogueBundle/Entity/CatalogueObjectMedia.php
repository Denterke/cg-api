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
 * @ORM\Table(name="catalogue_object_media")
 */
class CatalogueObjectMedia
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
     * @var CatalogueObject
     *
     * @ORM\ManyToOne(targetEntity="CatalogueObject", inversedBy="images")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id")
     */
    protected $object;

    /**
     * @var Media
     *
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"})
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
}
