<?php

namespace Farpost\CatalogueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
     * @var string
     *
     * @ORM\Column(name="original_filename", type="string", length=255)
     */
    protected $originalFilename;

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
     * @param UploadedFile $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * Get file
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        $clientFileName = $this->getFile()->getClientOriginalName();
        $clientExtName = $this->getFile()->getClientOriginalExtension();

        $this->originalFilename = $clientFileName;
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
