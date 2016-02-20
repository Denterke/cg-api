<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 07/08/15
 * Time: 18:09
 */

namespace Farpost\CatalogueBundle\Serializer;


class CategoryEdgeSerializer
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
                'id' => 'ce' . $object->getId(),
                'source' => 'c' . $object->getParent()->getId(),
                'target' => 'c' . $object->getChild()->getId(),
                'type' => 'categoryedge'
            ]
        ];
    }

}