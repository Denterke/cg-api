<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 13/07/15
 * Time: 10:59
 */

namespace Farpost\CatalogueBundle\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Farpost\CatalogueBundle\Entity\CatalogueImage;

class ImageManager {

    private $doctrine;
    private $imageHandling;

    public function __construct($imageHandling, $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->imageHandling = $imageHandling;
    }

    public function createImagesFromFile(UploadedFile $file)
    {
        $tmpPath = $file->getPath();
        $clientExtension = $file->getClientOriginalExtension();

        $standardName = $file->getFilename();
        $standardFullPath = join('/', [$tmpPath, $standardName]);

        $thumbnailName = join('.', [uniqid('', true), $clientExtension]);
        $thumbnailFullPath = join('/', [$tmpPath, $thumbnailName]);

        $this->imageHandling->open($standardFullPath)
            ->resize(100, 100)
            ->save($thumbnailFullPath)
        ;

        $standardFile = new File($standardFullPath);
        $thumbnailFile = new File($thumbnailFullPath);

        $standard = new CatalogueImage();
        $standard->setFile($standardFile)
            ->refreshUpdated()
        ;
        $thumbnail = new CatalogueImage();
        $thumbnail->setFile($thumbnailFile)
            ->refreshUpdated()
        ;

        return [
            'standard' => $standard,
            'thumbnail' => $thumbnail
        ];
    }

}