<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 03/08/15
 * Time: 11:04
 */

namespace Farpost\POIBundle\Serializer;


class PointSerializer
{
    const FULL_CARD = 0;

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

    public function fullCard($object)
    {
        return [
            'id' => $object->getId(),
            'typeId' => $object->getType()->getId(),
            'label' => $object->getLabel(),
            'content' => $object->getContent(),
            'lon' => $object->getRealLon(),
            'lat' => $object->getRealLat(),
            'level' => $object->getRealLevel(),
            'nodeId' => $object->getNode() ? $object->getNode()->getId() : null,
            'visible' => $object->getVisible()
        ];
    }
}