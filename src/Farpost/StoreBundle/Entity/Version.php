<?php

namespace Farpost\StoreBundle\Entity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Versions
 *
 * @ORM\Table(name="versions")
 * @ORM\Entity(repositoryClass="Farpost\StoreBundle\Entity\VersionRepository")
 */
class Version
{
    const ZIP_PLANS = -58;
    const ZIP_LIKE_MAPS_VL = -57;

    const CATALOG_V2 = -21;
    const CATALOG = -20;

    const GRAPH_DUMP = -19;
    //the shittiest shit ever
    const MAP = 1000;
    const LEVEL_0 = 0;
    const LEVEL_1 = 1;
    const LEVEL_2 = 2;
    const LEVEL_3 = 3;
    const LEVEL_4 = 4;
    const LEVEL_5 = 5;
    const LEVEL_6 = 6;
    const LEVEL_7 = 7;
    const LEVEL_8 = 8;
    const LEVEL_9 = 9;
    const LEVEL_10 = 10;
    const LEVEL_11 = 11;
    const LEVEL_12 = 12;
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var date
     *
     * @ORM\Column(name="v_datetime", type="integer")
     */
    protected $v_datetime;

    /**
     * @var string
     *
     * @ORM\Column(name="base", type="string")
     */
    protected $base;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer")
     */
    protected $type;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_processing", type="boolean", options={"default" = 0})
     */
    protected $isProcessing;

    /**
     * @var integer
     *
     * @ORM\Column(name="version_number", type="integer", options={"default" = 0})
     */
    protected $versionNumber;

    /**
     * @var integer
     *
     * @ORM\Column(name="file_size", type="float", options={"default" = 0}, nullable=true)
     */
    protected $fileSize;

    /**
     * @var string
     *
     * @ORM\Column(name="checksum", type="string", nullable=true)
     */
    protected $checksum;

    public function __construct()
    {
        $this->isProcessing = false;
        $this->versionNumber = -1;
        $this->fileSize = 0;
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

    public function setId($id)
    {
        $this->id = $id;
    }

    public function isPlan()
    {
        return $this->type >= 0;
    }

    /**
     * @return boolean
     */
    public function isMbtiles()
    {
        $mbtileTypes = [
            Version::LEVEL_0, Version::LEVEL_1, Version::LEVEL_2, Version::LEVEL_3,
            Version::LEVEL_4, Version::LEVEL_5, Version::LEVEL_6, Version::LEVEL_7,
            Version::LEVEL_8, Version::LEVEL_9, Version::LEVEL_10, Version::LEVEL_11,
            Version::LEVEL_12, Version::MAP
        ];
        return in_array($this->getType(), $mbtileTypes);
    }


    /**
     * Set v_datetime
     *
     * @param integer $vDatetime
     * @return Version
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

    /**
     * Set base
     *
     * @param string $base
     * @return Version
     * @throws \Exception
     */
    public function setBase($base)
    {
        $this->base = $base;

        return $this;
    }

    public function setBaseWithAttributes($base)
    {
        $this->base = $base;
        //do smth with it, please
        $filePath = self::getStaticDir() . "/$base";
        $fileSize = filesize($filePath);
        if (!$fileSize) {
            throw new \Exception("filesize($filePath) failed");
        }
        $fileSize = $fileSize / 1024 / 1024;
        $this->setFileSize($fileSize)
            ->setChecksum(md5_file($filePath));

        return $this;
    }

    /**
     * Get base
     *
     * @return string
     */
    public function getBase()
    {
        return $this->base;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return Version
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

    static public function typeToString($type)
    {
        switch($type) {
            case self::MAP:
                return 'Карта ДВФУ';
            case self::CATALOG:
                return 'Справочник старого формата (не поддерживается)';
            case self::CATALOG_V2:
                return 'Справочник';
            case self::ZIP_PLANS:
                return 'Архивированные тайлы';
            case self::ZIP_LIKE_MAPS_VL:
                return 'Архив как на картах VL';
            case self::GRAPH_DUMP:
                return 'Дамп базы картографов';
            case self::LEVEL_0:
            case self::LEVEL_1:
            case self::LEVEL_2:
            case self::LEVEL_3:
            case self::LEVEL_4:
            case self::LEVEL_5:
            case self::LEVEL_6:
            case self::LEVEL_7:
            case self::LEVEL_8:
            case self::LEVEL_9:
            case self::LEVEL_10:
            case self::LEVEL_11:
            case self::LEVEL_12:
                return "План уровня {$type}";
            default:
                return 'Неизвестный файл';
        }
    }

    static public function isTypeLevel($type)
    {
        return $type >= self::LEVEL_0 && $type <= self::LEVEL_12;
    }

    /**
     * Set isProcessing
     *
     * @param boolean $isProcessing
     * @return Version
     */
    public function setIsProcessing($isProcessing)
    {
        $this->isProcessing = $isProcessing;

        return $this;
    }

    /**
     * Get isProcessing
     *
     * @return boolean
     */
    public function getIsProcessing()
    {
        return $this->isProcessing;
    }

    /**
     * Set versionNumber
     *
     * @param integer $versionNumber
     * @return Version
     */
    public function setVersionNumber($versionNumber)
    {
        $this->versionNumber = $versionNumber;

        return $this;
    }

    /**
     * Get versionNumber
     *
     * @return integer
     */
    public function getVersionNumber()
    {
        return $this->versionNumber;
    }

    /**
     * Set fileSize
     *
     * @param float $fileSize
     * @return Version
     */
    public function setFileSize($fileSize)
    {
        $this->fileSize = $fileSize;

        return $this;
    }

    /**
     * Get fileSize
     *
     * @return float
     */
    public function getFileSize()
    {
        return $this->fileSize;
    }

    /**
     * @return string
     */
    public function getFullPath()
    {
        $fileName = $this->getBase();
        return self::getStaticDir() . "/$fileName";
    }

    /**
     * Set checksum
     *
     * @param string $checksum
     * @return Version
     */
    public function setChecksum($checksum)
    {
        $this->checksum = $checksum;

        return $this;
    }

    /**
     * Get checksum
     *
     * @return string 
     */
    public function getChecksum()
    {
        return $this->checksum;
    }

    static public function getStaticDir()
    {
        return dirname(__FILE__) . "/../../../../web/static";
    }
}
