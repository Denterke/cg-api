<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 31/07/15
 * Time: 13:02
 */

namespace Farpost\POIBundle\Admin;


use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class TypeAdmin extends Admin
{
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', 'text', [
                'label' => 'label.name'
            ])
            ->add('alias', 'text', [
                'label' => 'label.alias'
            ])
            ->add('group', 'sonata_type_model_list', [
                'label' => 'label.group'
            ])
            ->add('visible', 'checkbox', [
                'label' => 'label.visible',
                'required' => false
            ])
//            ->add('icon', 'sonata_media_type', [
//                'label' => 'label.icon',
//                'required' => false,
//                'provider' => 'sonata.media.provider.image',
//                'context' => 'icons'
//            ])
        ;
    }

    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name', null, [
                'label' => 'label.name'
            ])
            ->add('alias', null, [
                'label' => 'label.alias'
            ])
            ->add('group.name', null, [
                'label' => 'label.group'
            ])
            ->add('visible', null, [
                'label' => 'label.visible'
            ])
        ;
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('group.name', null, [
                'label' => 'label.group'
            ])
            ->add('name', null, [
                'label' => 'label.name'
            ])
        ;
    }
}