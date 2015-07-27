<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 27/07/15
 * Time: 15:15
 */

namespace Farpost\NewsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class ImageSet
 * @package Farpost\NewsBundle\Entity
 *
 * @ORM\Table(name="news_imagesets")
 * @ORM\Entity
 */
class ImageSet
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
     * @var Image
     *
     * @ORM\OneToOne(targetEntity="Image", cascade={"persist"}, orphanRemoval=true)
     * @ORM\JoinColumn(name="small_image_id", referencedColumnName="id")
     */
    protected $smallImage;

    /**
     * @var Image
     *
     * @ORM\OneToOne(targetEntity="Image", cascade={"persist"}, orphanRemoval=true)
     * @ORM\JoinColumn(name="big_image_id", referencedColumnName="id")
     */
    protected $bigImage;

    /**
     * @var Image
     *
     * @ORM\OneToOne(targetEntity="Image", cascade={"persist"}, orphanRemoval=true)
     * @ORM\JoinColumn(name="src_image_id", referencedColumnName="id")
     */
    protected $srcImage;

    /**
     * @var File
     */
    protected $file;

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
     * Set smallImage
     *
     * @param \Farpost\NewsBundle\Entity\Image $smallImage
     * @return ImageSet
     */
    public function setSmallImage(\Farpost\NewsBundle\Entity\Image $smallImage = null)
    {
        $this->smallImage = $smallImage;

        return $this;
    }

    /**
     * Get smallImage
     *
     * @return \Farpost\NewsBundle\Entity\Image 
     */
    public function getSmallImage()
    {
        return $this->smallImage;
    }

    /**
     * Set bigImage
     *
     * @param \Farpost\NewsBundle\Entity\Image $bigImage
     * @return ImageSet
     */
    public function setBigImage(\Farpost\NewsBundle\Entity\Image $bigImage = null)
    {
        $this->bigImage = $bigImage;

        return $this;
    }

    /**
     * Get bigImage
     *
     * @return \Farpost\NewsBundle\Entity\Image 
     */
    public function getBigImage()
    {
        return $this->bigImage;
    }

    /**
     * Set srcImage
     *
     * @param \Farpost\NewsBundle\Entity\Image $srcImage
     * @return ImageSet
     */
    public function setSrcImage(\Farpost\NewsBundle\Entity\Image $srcImage = null)
    {
        $this->srcImage = $srcImage;

        return $this;
    }

    /**
     * Get srcImage
     *
     * @return \Farpost\NewsBundle\Entity\Image 
     */
    public function getSrcImage()
    {
        return $this->srcImage;
    }

    /**
     * Sets file
     *
     * @param File $file
     * @return ImageSet $this
     */

    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return File $file
     */
    public function getFile()
    {
        return $this->file;
    }
}
