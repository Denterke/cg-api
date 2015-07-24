<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 24/07/15
 * Time: 11:00
 */

namespace Farpost\StoreBundle\Services;


use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\File\File;
use Farpost\StoreBundle\Entity\Version;

class VersionManager
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Returns last versions of each type
     * @return mixed
     */
    protected function getLastVersions()
    {
        return $this->em
            ->getRepository('FarpostStoreBundle:Version')
            ->getLastVersions()
        ;
    }

    protected function createZipArchive($fullArchiveName)
    {
        $zip = new \ZipArchive();
        if ($zip->open($fullArchiveName, \ZipArchive::CREATE) !== TRUE) {
            throw new \Exception('Возникли проблемы при создании zip архива.');
        }
        return $zip;
    }

    /**
     * @param null $timestamp
     * @return string
     * @throws \Exception
     */
    public function zipLikeCampusGuide($timestamp = null)
    {
        if (!$timestamp) {
            $timestamp = (new \DateTime())->getTimestamp();
        }
        $versions = $this->getLastVersions();
        $archiveName = "plans_zip_{$timestamp}.zip";
        $zip = $this->createZipArchive(STATIC_DIR . "/$archiveName");
        $idx = 0;
        foreach ($versions as $version) {
            if ($version->isMbtiles()) {
                $zip->addFile($version->getFullPath(), ++$idx . '.mbtiles');
            }
        }
        $zip->close();
        return $archiveName;
    }

    public function zipLikeMapsVL()
    {
        $versions = $this->getLastVersions();
        $filesForZip = [];
        $lastMapsZipVersion = 0;
        foreach($versions as $version) {
            if ($version->getType() === Version::ZIP_LIKE_MAPS_VL) {
                $lastMapsZipVersion = $version->getVersionNumber();
                continue;
            }
            if ($version->isMbtiles()) {
                //LEVEL_1 => 101
                //LEVEL_2 => 102
                //..
                //MAP => 159
                $type = abs($version->getType());
                $name = '1' . ($type > 10 ? $type : "0$type") . '_' . $version->getVersionNumber() . '.db';
                $filesForZip[] = [
                    'name' => $name,
                    'path' => $version->getFullPath()
                ];
                continue;
            }
            if ($version->getType() === Version::CATALOG_V2) {
                $filesForZip[] = [
                    'name' => 'catalog.db',
                    'path' => $version->getFullPath()
                ];
                continue;
            }
        }
        $lastMapsZipVersion++;
        $archiveName = "200_$lastMapsZipVersion.db";
        $fullArchiveName = STATIC_DIR . "/$archiveName";
        $zip = $this->createZipArchive($fullArchiveName);
        var_dump($filesForZip);
        foreach($filesForZip as $fileForZip) {
            $zip->addFile($fileForZip['path'], $fileForZip['name']);
        }
        $zip->close();

        $zipSize = filesize($fullArchiveName);
        if (!$zipSize) {
            throw new \Exception("filesize($fullArchiveName) failed");
        }
        $zipSize = $zipSize / 1024 / 1024;
        $mapsZipVersion = new Version();
        $mapsZipVersion->setBase($archiveName)
            ->setVDatetime((new \DateTime())->getTimestamp())
            ->setVersionNumber($lastMapsZipVersion)
            ->setType(Version::ZIP_LIKE_MAPS_VL)
        ;
        $this->em->persist($mapsZipVersion);
        $this->em->flush();
    }

    /**
     * @param $fileType
     * @param File $file
     *
     * @return Version
     * @throws \Exception
     */
    public function createVersion($fileType, File $file)
    {
        $dt = new \DateTime();
        $timestamp = $dt->getTimestamp();

        $version = new Version();
        $version->setType($fileType)
            ->setVDatetime($timestamp);
        if (!$version->isMbtiles()) {
            return null;
        }

        $formattedDate = date('Ymd_Gis', $dt->getTimestamp());
        $newName = ($fileType == Version::MAP ? "map_" : "plan_{$fileType}_") . $formattedDate . '.mbtiles';
        $file->move(STATIC_DIR, $newName);

        $lastVersionOfThisType = $this->em
            ->getRepository('FarpostStoreBundle:Version')
            ->getLastVersionOfType($fileType)
        ;
        $lastVersionNumber = $lastVersionOfThisType ? $lastVersionOfThisType->getVersionNumber() : 0;

        $version->setBase($newName)
            ->setVersionNumber(++$lastVersionNumber)
        ;
        $this->em->persist($version);
        $this->em->flush();

        $this->zipLikeMapsVL();
    }


}