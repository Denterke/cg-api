<?php

namespace Farpost\CatalogueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
/**
 *
 * @ORM\Table(name="catalogue_images")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */

class CatalogueImage
{
    const UPLOAD_ROOT_PATH = 'uploads/catalogue/images';

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
     * @ORM\Column(name="filename", type="string", length=255)
     */
    protected $filename;

    /**
     * @var datetime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    protected $updatedAt;

    private $file;

    /**
     * Sets file
     *
     * @param File $file
     * @return CatalogueImage
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Returns web path to image
     *
     * @return string
     */
    public function getWebPath()
    {
        return $this->getFilename()
            ? '/' . self::UPLOAD_ROOT_PATH . '/' . $this->getFilename()
            : null;
    }

    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        if ($this->filename) {
            $this->removeFile();
        }

        $clientExtName = $this->getFile()->guessExtension();
        $newFileName = join('.', [uniqid('', true), $clientExtName]);

        $this->getFile()->move(
            self::UPLOAD_ROOT_PATH,
            $newFileName
        );

        $this->filename = $newFileName;
        $this->setFile(null);
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */

    public function lifecycleFileUpload()
    {
        $this->upload();
    }

    public function refreshUpdated()
    {
        $this->setUpdatedAt(new \DateTime("now"));
    }

    /**
     * @ORM\PreRemove
     */
    public function removeFile()
    {
        $fileName = self::UPLOAD_ROOT_PATH . '/' . $this->getFilename();
        if (file_exists($fileName)) {
            unlink($fileName);
        }
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
     * Set filename
     *
     * @param string $filename
     * @return CatalogueImage
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string 
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return CatalogueImage
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
