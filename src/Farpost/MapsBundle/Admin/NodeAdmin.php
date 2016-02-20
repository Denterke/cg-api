<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 09/07/15
 * Time: 12:25
 */

namespace Farpost\MapsBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class NodeAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('id', 'text', [
                'label' => 'label.id',
                'required' => false
            ])
            ->add('alias', 'text', [
                'label' => 'label.alias',
                'required' => false
            ])
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('alias', null, [
                'label' => 'label.alias'
            ])
            ->add('type.alias', null, [
                'label' => 'label.type'
            ])
            ->add('level.alias', null, [
                'label' => 'label.alias'
            ])
            ->add('building.alias', null, [
                'label' => 'label.building'
            ])
            ->addIdentifier('id', null, [
                'label' => 'label.id'
            ])
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('alias', null, ['label' => 'label.alias'])
            ->add('type', null, ['label' => 'label.type'], null, [
                'multiple' => true,
                'property' => 'alias'
            ])
            ->add('building', null, ['label' => 'label.building'], null, [
                'multiple' => true,
                'property' => 'number'
            ])
        ;
    }
}