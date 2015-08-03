<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 31/07/15
 * Time: 17:56
 */

namespace Farpost\POIBundle\Serializer;


class GroupSerializer
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
            'name' => $object->getName(),
            'alias' => $object->getAlias(),
            'visible' => $object->getVisible()
        ];
    }

}