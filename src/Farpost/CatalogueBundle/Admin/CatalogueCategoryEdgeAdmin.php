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


class CatalogueCategoryEdgeAdmin extends Admin {
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
//            ->add('parent', 'sonata_type_model', ['label' => 'label.parent_category'])
            ->add('child', 'sonata_type_model', ['label' => 'label.child_category'])
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('parent')
            ->add('child')
        ;
    }
}