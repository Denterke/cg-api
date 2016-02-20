<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 29/07/15
 * Time: 11:06
 */

namespace Farpost\NewsBundle\Serializer;


abstract class Serializer
{
    abstract public function serialize($objects);
    abstract public function serializeOne($object);
}