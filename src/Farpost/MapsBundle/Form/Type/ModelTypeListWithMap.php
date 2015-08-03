<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 03/08/15
 * Time: 10:30
 */

namespace Farpost\MapsBundle\Form\Type;


use Sonata\AdminBundle\Form\Type\ModelTypeList;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Test\FormInterface;

class ModelTypeListWithMap extends ModelTypeList
{
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'sonata_type_model_list_with_map';
    }

//    /**
//     * {@inheritDoc}
//     */
//    public function buildView(FormView $view, FormInterface $form, array $options)
//    {
//
//    }

}