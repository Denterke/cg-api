<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 27/07/15
 * Time: 13:49
 */

namespace Farpost\NewsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator as Assert;

/**
 * Class Article
 * @package Farpost\NewsBundle\Entity
 *
 * @ORM\Table(name="news_articles")
 * @ORM\Entity(repositoryClass="Farpost\NewsBundle\Entity\ArticleRepository")
 */
class Article
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text")
     */
    protected $body;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dt", type="datetime")
     */
    protected $dt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="published", type="boolean")
     */
    protected $published;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ArticleImage", mappedBy="article", cascade={"all"}, orphanRemoval=true)
     */
    protected $images;

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
     * Set title
     *
     * @param string $title
     * @return Article
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
     * @return Article
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
     * Set dt
     *
     * @param \DateTime $dt
     * @return Article
     */
    public function setDt($dt)
    {
        $this->dt = $dt;

        return $this;
    }

    /**
     * Get dt
     *
     * @return \DateTime 
     */
    public function getDt()
    {
        return $this->dt;
    }

    /**
     * Set published
     *
     * @param boolean $published
     * @return Article
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return boolean 
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set imageSet
     *
     * @param \Farpost\NewsBundle\Entity\ImageSet $imageSet
     * @return Article
     */
    public function setImageSet(\Farpost\NewsBundle\Entity\ImageSet $imageSet = null)
    {
        $this->imageSet = $imageSet;

        return $this;
    }

    /**
     * Get imageSet
     *
     * @return \Farpost\NewsBundle\Entity\ImageSet 
     */
    public function getImageSet()
    {
        return $this->imageSet;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    /**
     * Add images
     *
     * @param \Farpost\NewsBundle\Entity\ArticleImage $image
     * @return Article
     */
    public function addImage(\Farpost\NewsBundle\Entity\ArticleImage $image)
    {
        $image->setArticle($this);
        $this->images[] = $image;

        return $this;
    }

    /**
     * Remove images
     *
     * @param \Farpost\NewsBundle\Entity\ArticleImage $image
     */
    public function removeImage(\Farpost\NewsBundle\Entity\ArticleImage $image)
    {
        $image->setArticle(null);
        $this->images->removeElement($image);
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
