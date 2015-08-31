<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 31/07/15
 * Time: 17:56
 */

namespace Farpost\POIBundle\Serializer;


use Sonata\MediaBundle\Provider\ImageProvider;

class TypeSerializer
{
    const FULL_CARD = 0;

    /**
     * @var ImageProvider
     */
    private $imageProvider;


    public function __construct(ImageProvider $imageProvider)
    {
        $this->imageProvider = $imageProvider;
    }

    /**
     * @param array $objects
     * @param integer $type
     * @return array
     */
    public function serialize($objects, $type = self::FULL_CARD)
    {
        $results = [];
        foreach ($objects as $object) {
            $results[] = $this->serializeOne($object, $type);
        }
        return $results;
    }

    /**
     * @param $object
     * @param $type
     */
    public function serializeOne($object, $type)
    {
        switch ($type) {
            case self::FULL_CARD:
                return $this->fullCard($object);
            default:
                return $this->fullCard($object);
        }
    }

    /**
     *
     *
     * @param $object
     * @return array
     */
    public function fullCard($object)
    {
        $result = [
            'icon' => '',
            'width' => 0,
            'height' => 0
        ];

        if ($object->getIcon()) {
            $format = $this->imageProvider->getFormatName($object->getIcon(), 'icon');
            $properties = $this->imageProvider->getHelperProperties($object->getIcon(), $format);
            $result = [
                'icon' => $this->imageProvider->generatePublicUrl($object->getIcon(), $format),
                'width' => $properties['width'],
                'height' => $properties['height']
            ];
        }

        $result = $result + [
            'id' => $object->getId(),
            'groupId' => $object->getGroup()->getId(),
            'name' => $object->getName(),
            'alias' => $object->getAlias(),
            'visible' => $object->getVisible(),
        ];

        return $result;
    }

}