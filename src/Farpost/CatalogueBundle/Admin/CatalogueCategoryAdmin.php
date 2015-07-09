<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 09/07/15
 * Time: 14:52
 */

namespace Farpost\CatalogueBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;


class CatalogueCategoryAdmin extends Admin {
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('is_organization', 'checkbox', [
                'label' => 'label.is_organization',
                'required' => false
            ])
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
            ->add('children', 'sonata_type_collection', [
                'by_reference' => false,
                'label' => 'label.children_categories'
            ], [
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'id'
            ])
            ->add('objects', 'sonata_type_collection', [
                'by_reference' => false,
                'label' => 'label.children_objects'
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
            ->add('isOrganization', null, ['operator_type' => 'sonata_type_boolean', 'label' => 'label.is_organization'])
            ->add('name', null, ['label' => 'label.name'])
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('isOrganization', null, ['label' => 'label.is_organization'])
            ->add('name', null, ['label' => 'label.name'])
            ->add('description', null, ['label' => 'label.description'])
            ->add('phone', null, ['label' => 'label.phone'])
            ->add('site', null, ['label' => 'label.site'])
        ;
    }
}