<?php

namespace Farpost\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * News
 *
 * @ORM\Table(name="news")
 * @ORM\Entity(repositoryClass="Farpost\StoreBundle\Entity\NewsRepository")
 */
class News
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
     * @var integer
     *
     * @ORM\Column(name="vk_id", type="integer")
     */
    protected $vkId;

    /**
     * @var integer
     *
     * @ORM\Column(name="dt", type="integer")
     */
    protected $dt;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", nullable=true)
     */
    protected $title;

    /** 
     * @var text
     *
     * @ORM\Column(name="body", type="text")
     */
    protected $body;

    /**
     * @var string
     *
     * @ORM\Column(name="main_img", type="string", nullable=true)
     */
    protected $img;

    /**
     * @ORM\OneToMany(targetEntity="Link", mappedBy="news")
     */
    protected $links;

    /**
     * @ORM\OneToMany(targetEntity="Image", mappedBy="news")
     */
    protected $images;

    /**
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    protected $active = true;

    public function __construct()
    {
        $this->links = new ArrayCollection();
        $this->images = new ArrayCollection();
    } 

    /**
     * Set id
     *
     * @param integer $id
     * @return News
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set dt
     *
     * @param integer $dt
     * @return News
     */
    public function setDt($dt)
    {
        $this->dt = $dt;

        return $this;
    }

    /**
     * Get dt
     *
     * @return integer 
     */
    public function getDt()
    {
        return $this->dt;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return News
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set body
     *
     * @param string $body
     * @return News
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string 
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set img
     *
     * @param string $img
     * @return News
     */
    public function setImg($img)
    {
        $this->img = $img;

        return $this;
    }

    /**
     * Get img
     *
     * @return string 
     */
    public function getImg()
    {
        return $this->img;
    }

    /**
     * Add links
     *
     * @param \Farpost\StoreBundle\Entity\Link $links
     * @return News
     */
    public function addLink(\Farpost\StoreBundle\Entity\Link $links)
    {
        $this->links[] = $links;

        return $this;
    }

    /**
     * Remove links
     *
     * @param \Farpost\StoreBundle\Entity\Link $links
     */
    public function removeLink(\Farpost\StoreBundle\Entity\Link $links)
    {
        $this->links->removeElement($links);
    }

    /**
     * Get links
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * Add images
     *
     * @param \Farpost\StoreBundle\Entity\Image $images
     * @return News
     */
    public function addImage(\Farpost\StoreBundle\Entity\Image $images)
    {
        $this->images[] = $images;

        return $this;
    }

    /**
     * Remove images
     *
     * @param \Farpost\StoreBundle\Entity\Image $images
     */
    public function removeImage(\Farpost\StoreBundle\Entity\Image $images)
    {
        $this->images->removeElement($images);
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

    /**
     * Set active
     *
     * @param boolean $active
     * @return News
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set vkId
     *
     * @param integer $vkId
     * @return News
     */
    public function setVkId($vkId)
    {
        $this->vkId = $vkId;

        return $this;
    }

    /**
     * Get vkId
     *
     * @return integer 
     */
    public function getVkId()
    {
        return $this->vkId;
    }
}
