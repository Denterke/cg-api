<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 07/08/15
 * Time: 18:32
 */

namespace Farpost\CatalogueBundle\Serializer;


class CategoryNodeEdgeSerializer
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
                'id' => 'cne' . $object->getId(),
                'source' => 'c' . $object->getCategory()->getId(),
                'target' => 'o' . $object->getObject()->getId(),
                'type' => 'categorynodeedge'
            ]
        ];
    }

}