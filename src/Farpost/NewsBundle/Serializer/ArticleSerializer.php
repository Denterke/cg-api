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
use Symfony\Component\DependencyInjection\ContainerInterface;

class ArticleSerializer extends Serializer
{
    private $imageProvider;
    private $container;

    public function __construct(ContainerInterface $container, ImageProvider $imageProvider)
    {
        $this->imageProvider = $imageProvider;
        $this->container = $container;
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
            $src = $this->imageProvider->getFormatName($media, 'reference');
            $big = $this->imageProvider->getFormatName($media, 'big');
            $small = $this->imageProvider->getFormatName($media, 'small');
            $prefix = "http://" . join(':', [$this->container->get('request')->getHost(), $this->container->get('request')->getPort()]);
            $srcProperties = $this->imageProvider->getHelperProperties($media, 'reference');
            $images[] =
                [
                    'src' => $prefix . $this->imageProvider->generatePublicUrl($media, $src),
                    'src_big' => $prefix . $this->imageProvider->generatePublicUrl($media, $big),
                    'src_small' => $prefix . $this->imageProvider->generatePublicUrl($media, $small),
                    'width' => $srcProperties['width'],
                    'height' => $srcProperties['height']
                ];
        }
        $result['images'] = $images;

        return $result;
    }

}