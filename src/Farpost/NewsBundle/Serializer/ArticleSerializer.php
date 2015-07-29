<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 29/07/15
 * Time: 11:08
 */

namespace Farpost\NewsBundle\Serializer;


use Sonata\MediaBundle\Entity\MediaManager;
use Sonata\MediaBundle\Provider\ImageProvider;

class ArticleSerializer extends Serializer
{
    private $imageProvider;

    public function __construct(ImageProvider $imageProvider)
    {
        $this->imageProvider = $imageProvider;
    }

    public function serialize($objects)
    {
        $result = [];
        foreach($objects as $object) {
            $result[] = $this->serializeOne($object);
        }

        return $result;
    }

    public function serializeOne($object)
    {
        $result = [
            'date' => $object->getDt()->getTimestamp(),
            'title' => $object->getTitle(),
            'body' => $object->getBody(),
            'id' => $object->getId()
        ];

        $images = [];
        foreach($object->getImages() as $imageAssociation) {
            $media = $imageAssociation->getMedia();
            $preview = $this->imageProvider->getFormatName($media, 'preview');
            $small = $this->imageProvider->getFormatName($media, 'small');
            $big = $this->imageProvider->getFormatName($media, 'big');
            $images[] =
                [
                    'src' => $this->imageProvider->generatePublicUrl($media, $preview),
                    'src_big' => $this->imageProvider->generatePublicUrl($media, $small),
                    'src_small' => $this->imageProvider->generatePublicUrl($media, $big)
                ];
        }
        $result['images'] = $images;

        return $result;
    }

}