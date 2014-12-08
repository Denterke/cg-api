<?php

namespace Farpost\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Images
 *
 * @ORM\Table(name="images")
 * @ORM\Entity
 */
class Image
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
    * @ORM\Column(name="src", type="string")
    */
   protected $src;

   /**
    * @var string
    *
    * @ORM\Column(name="src_small", type="string")
    */
   protected $srcSmall;

   /**
    * @var string
    *
    * @ORM\Column(name="src_big", type="string")
    */
   protected $srcBig;

   /**
    * @var integer
    *
    * @ORM\Column(name="width", type="integer")
    */
   protected $width;

   /**
    * @var integer
    *
    * @ORM\Column(name="height", type="integer")
    */
   protected $height;

    /**
     * @ORM\ManyToOne(targetEntity="News", inversedBy="images")
     * @ORM\JoinColumn(name="news_id", referencedColumnName="id", nullable=false)
     */
    protected $news;

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
     * Set src
     *
     * @param string $src
     * @return Image
     */
    public function setSrc($src)
    {
        $this->src = $src;

        return $this;
    }

    /**
     * Get src
     *
     * @return string 
     */
    public function getSrc()
    {
        return $this->src;
    }

    /**
     * Set srcSmall
     *
     * @param string $srcSmall
     * @return Image
     */
    public function setSrcSmall($srcSmall)
    {
        $this->srcSmall = $srcSmall;

        return $this;
    }

    /**
     * Get srcSmall
     *
     * @return string 
     */
    public function getSrcSmall()
    {
        return $this->srcSmall;
    }

    /**
     * Set srcBig
     *
     * @param string $srcBig
     * @return Image
     */
    public function setSrcBig($srcBig)
    {
        $this->srcBig = $srcBig;

        return $this;
    }

    /**
     * Get srcBig
     *
     * @return string 
     */
    public function getSrcBig()
    {
        return $this->srcBig;
    }

    /**
     * Set width
     *
     * @param integer $width
     * @return Image
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get width
     *
     * @return integer 
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param integer $height
     * @return Image
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return integer 
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set news
     *
     * @param \Farpost\StoreBundle\Entity\News $news
     * @return Image
     */
    public function setNews(\Farpost\StoreBundle\Entity\News $news)
    {
        $this->news = $news;

        return $this;
    }

    /**
     * Get news
     *
     * @return \Farpost\StoreBundle\Entity\News 
     */
    public function getNews()
    {
        return $this->news;
    }

    public function getSrcURL($hostname)
    {
        return "http://$hostname/static/newsImgs" . "/" . $this->getSrc();
    }

    public function getSrcBigURL($hostname)
    {
        return "http://$hostname/static/newsImgs" . "/" . $this->getSrcBig();
    }

    public function getSrcSmallURL($hostname)
    {
        return "http://$hostname/static/newsImgs" . "/" . $this->getSrcSmall();
    }
}
