<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 06/08/15
 * Time: 15:33
 */

namespace Farpost\CatalogueBundle\Serializer;


class ObjectSerializer
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
        return [
            'data' => [
                'realId' => $object->getId(),
                'id' => 'o' . $object->getId(),
                'name' => $object->getName(),
    //            'ways' => [],
                'type' => 'object'
            ]
        ];
    }

}