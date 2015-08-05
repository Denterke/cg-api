<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 13/07/15
 * Time: 10:59
 */

namespace Farpost\CatalogueBundle\Services;

use Farpost\StoreBundle\Entity\Image;
use Gregwar\ImageBundle\Services\ImageHandling;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;

class ImageManager {

    private $doctrine;
    /**
     * @var ImageHandling
     */
    private $imageHandling;

    public function __construct(ImageHandling $imageHandling, $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->imageHandling = $imageHandling;
    }

    public function createImages(UploadedFile $file, $params)
    {
        $clientExtension = $file->getClientOriginalExtension();

        $file = $file->move('tmp', join('.', [uniqid('', true), $clientExtension]));
        $tmpPath = $file->getPath();

        $originalName = $file->getFilename();
        $originalFullPath = join('/', [$tmpPath, $originalName]);

        $processedImages = [
            'original' => new File($originalFullPath)
        ];

        foreach($params as $imageParams) {
            $processedImageName = join('.', [uniqid('', true), $clientExtension]);
            $processedImageFullPath = join('/', [$tmpPath, $processedImageName]);
            $this->imageHandling->open($originalFullPath)
                ->resize($imageParams['width'], $imageParams['height'])
                ->save($processedImageFullPath)
            ;
            $processedImages[$imageParams['name']] = new File($processedImageFullPath);
        }

        return $processedImages;
    }

//    public function createImagesFromFile(UploadedFile $file)
//    {
//        $clientExtension = $file->getClientOriginalExtension();
//
//        $file = $file->move('tmp', join('.', [$file->getClientOriginalName(), $file->getClientOriginalExtension()]));
//        $tmpPath = $file->getPath();
//
//
//        $standardName = $file->getFilename();
//        $standardFullPath = join('/', [$tmpPath, $standardName]);
//
//        $thumbnailName = join('.', [uniqid('', true), $clientExtension]);
//        $thumbnailFullPath = join('/', [$tmpPath, $thumbnailName]);
//
//        $this->imageHandling->open($standardFullPath)
//            ->resize(100, 100)
//            ->save($thumbnailFullPath)
//        ;
//
//        $standardFile = new File($standardFullPath);
//        $thumbnailFile = new File($thumbnailFullPath);
//
//        $standard = new CatalogueImage();
//        $standard->setFile($standardFile)
//            ->refreshUpdated()
//        ;
//        $thumbnail = new CatalogueImage();
//        $thumbnail->setFile($thumbnailFile)
//            ->refreshUpdated()
//        ;
//
//        return [
//            'standard' => $standard,
//            'thumbnail' => $thumbnail
//        ];
//    }

}