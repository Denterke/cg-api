<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 09/07/15
 * Time: 12:25
 */

namespace Farpost\CatalogueBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class CatalogueObjectAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', 'text', ['label' => 'label.name'])
            ->add('description', 'textarea', [
                'label' => 'label.description',
                'required' => false
            ])
            ->add('phone', 'text', [
                'label' => 'label.phone',
                'required' => false
            ])
            ->add('site', 'text', [
                'label' => 'label.site',
                'required' => false
            ])
            ->add('categories', 'sonata_type_collection', [
                'by_reference' => false,
                'required' => false,
                'label' => 'label.parent_categories'
            ], [
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'id'
            ])
            -> add('schedule', 'sonata_type_collection', [
                'by_reference' => false,
                'required' => false,
                'label' => 'label.schedule'
            ], [
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'id'
            ])
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, ['label' => 'label.name'])
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('name', null, ['label' => 'label.name'])
            ->add('description', null, ['label' => 'label.description'])
            ->add('phone', null, ['label' => 'label.phone'])
            ->add('site', null, ['label' => 'label.site'])
        ;
    }

}