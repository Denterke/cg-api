<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 05/08/15
 * Time: 16:39
 */

namespace Farpost\CatalogueBundle\Serializer;


class CategorySerializer
{
    const EDITOR_CARD = 1;
    public function serialize($objects, $cardType)
    {
        $result = [];
        foreach($objects as $object) {
            $result[] = $this->serializeOne($object, $cardType);
        }

        return $result;
    }

    public function serializeOne($object, $cardType)
    {
        switch($cardType) {
            case self::EDITOR_CARD:
                return $this->editorCard($object);
            default:
                return $this->editorCard($object);
        }
    }

    public function editorCard($object)
    {
//        $ways = [];
//        foreach($object->getChildren() as $child) {
//            $ways[] = $child->getChild()->getId();
//        }
//
//        foreach ($object->getObjects() as $childObject) {
//            $ways[] = $childObject->getObject()->getId() * ObjectSerializer::ID_MULTIPLYER;
//        }

        return [
            'data' => [
                'id' => 'c' . $object->getId(),
                'realId' => $object->getId(),
                'name' => $object->getName(),
                'isRoot' => $object->getIsRoot(),
    //            'ways' => $ways,
                'type' => 'category'
            ]
        ];
    }

}