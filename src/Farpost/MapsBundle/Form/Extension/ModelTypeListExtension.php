<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 03/08/15
 * Time: 10:24
 */

namespace Farpost\MapsBundle\Form\Extension;



use Symfony\Component\Form\AbstractTypeExtension;

class ModelTypeListExtension extends AbstractTypeExtension
{
    public function getExtendedType()
    {
        return 'sonata_type_model_list';
    }

}