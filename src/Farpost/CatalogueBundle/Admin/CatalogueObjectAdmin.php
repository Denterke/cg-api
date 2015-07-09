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
            ->add('name', 'text', ['label' => 'Название'])
            ->add('description', 'textarea', ['label' => 'Описание'])
            ->add('phone', 'text', ['label' => 'Телефон'])
            ->add('site', 'text', ['label' => 'Сайт'])
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('name')
            ->add('description')
            ->add('phone')
            ->add('site')
        ;
    }

}