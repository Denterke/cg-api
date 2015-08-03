<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 31/07/15
 * Time: 12:47
 */

namespace Farpost\POIBundle\Admin;


use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class GroupAdmin extends Admin
{
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', 'text', [
                'label' => 'label.name',
            ])
            ->add('alias', 'text', [
                'label' => 'label.alias'
            ])
            ->add('visible', 'checkbox', [
                'label' => 'label.visible',
                'required' => false
            ])
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
            ->add('visible', null, [
                'label' => 'label.visible'
            ])
        ;
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, [
                'label' => 'label.name'
            ])
            ->add('alias', null, [
                'label' => 'label.name'
            ])
            ->add('visible', null, [
                'label' => 'label.visible',
            ], null, [
                'multiple' => true,
                'expanded' => true
            ])
        ;
    }

}