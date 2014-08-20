<?php

namespace Farpost\StoreBundle\Entity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Farpost\StoreBundle\Entity\Version;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Documents
 *
 * @ORM\Table(name="documents")
 * @ORM\Entity
 */
class Document
{
   /**
    * @ORM\Id
    * @ORM\Column(type="integer")
    * @ORM\GeneratedValue(strategy="AUTO")
    */
   private $id;

   /**
    * @ORM\Column(type="string", length=255, nullable=true)
    */
   private $path;

   /**
    * @ORM\Column(type="integer")
    */
   private $type;

   /**
    * @ORM\Column(type="integer")
    */
   private $v_datetime;

   /**
    * @Assert\File(maxSize="60000000")
    */
   private $file;

   /**
    * Sets file.
    *
    * @param UploadedFile $file
    */
   public function setFile(UploadedFile $file = null)
   {
      $this->file = $file;
   }

   /**
    * Get file.
    *
    * @return UploadedFile
    */
   public function getFile()
   {
      return $this->file;
   }

   public function getAbsolutePath()
   {
      return null === $this->path
         ? null
         : $this->getUploadRootDir().'/'.$this->path;
   }

   public function getWebPath()
   {
      return null === $this->path
         ? null
         : $this->getUploadDir().'/'.$this->path;
   }

   protected function getUploadRootDir()
   {
      // the absolute directory path where uploaded
      // documents should be saved
      return __DIR__.'/../../../../web/'.$this->getUploadDir();
   }

   protected function getUploadDir()
   {
      // get rid of the __DIR__ so it doesn't screw up
      // when displaying uploaded doc/image in the view.
      return 'uploads/documents';
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
     * Set path
     *
     * @param string $path
     * @return Document
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return Document
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }
    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

        // use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and then the
        // target filename to move to
        $this->getFile()->move(
            $this->getUploadRootDir(),
            $this->getFile()->getClientOriginalName()
        );

        // set the path property to the filename where you've saved the file
        $this->path = $this->getFile()->getClientOriginalName();

        // clean up the file property as you won't need it anymore
        $this->file = null;

        $dt = new \DateTime();
        $this->v_datetime = $dt->getTimestamp();
    }

    /**
     * Set v_datetime
     *
     * @param integer $vDatetime
     * @return Document
     */
    public function setVDatetime($vDatetime)
    {
        $this->v_datetime = $vDatetime;

        return $this;
    }

    /**
     * Get v_datetime
     *
     * @return integer 
     */
    public function getVDatetime()
    {
        return $this->v_datetime;
    }
}
