<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 04/08/15
 * Time: 12:04
 */

namespace Farpost\MapsBundle\Serializer;


class NodeSerializer
{
    const FULL_CARD = 0;
    const POSITION_CARD = 1;

    public function serialize($objects, $cardType = self::FULL_CARD)
    {
//        foreach($)
    }

    public function serializeOne($object, $cardType = self::FULL_CARD)
    {
        switch ($cardType) {
            case self::FULL_CARD:
                return $this->fullCard($object);
            case self::POSITION_CARD:
                return $this->positionCard($object);
            default:
                return $this->fullCard($object);
        }
    }

    public function positionCard($object)
    {
        return [
            'id' => $object->getId(),
            'lat' => $object->getLat(),
            'lon' => $object->getLon(),
            'level' => $object->getLevel()->getLevel()
        ];
    }

    public function fullCard($object)
    {
        return [
            'id' => $object->getId(),
            'lat' => $object->getLat(),
            'lon' => $object->getLon(),
            'alias' => $object->getAlias(),
            'type_id' => $object->getType()->getId()
        ];
    }

}