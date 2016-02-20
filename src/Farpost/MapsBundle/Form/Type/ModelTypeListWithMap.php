<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 03/08/15
 * Time: 10:30
 */

namespace Farpost\MapsBundle\Form\Type;


use Sonata\AdminBundle\Form\Type\ModelTypeList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ModelTypeListWithMap extends ModelTypeList
{
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'sonata_type_model_list_with_map';
    }

    public function getParent()
    {
        return 'sonata_type_model_list';
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['btn_map'] = $options['btn_map'];
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'model_manager'     => null,
            'class'             => null,
            'btn_add'           => 'link_add',
            'btn_list'          => 'link_list',
            'btn_delete'        => 'link_delete',
            'btn_map'           => 'link_map',
            'btn_catalogue'     => 'SonataAdminBundle'
        ));
    }

//    /**
//     * {@inheritDoc}
//     */
//    public function buildView(FormView $view, FormInterface $form, array $options)
//    {
//
//    }

}