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


class CatalogueCategoryObjectEdgeAdmin extends Admin {
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('isPrefix', 'checkbox', [
            'label' => 'label.isPrefix'
        ]);

        if ($this->getRoot()->getClass() !== 'Farpost\CatalogueBundle\Entity\CatalogueCategory') {
            $formMapper
                ->add('category', 'sonata_type_model', ['label' => 'label.parent_category'])
            ;
        }
        if ($this->getRoot()->getClass() !== 'Farpost\CatalogueBundle\Entity\CatalogueObject') {
            $formMapper
                ->add('object', 'sonata_type_model', ['label' => 'label.object'])
            ;
        }
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('category')
            ->add('object')
        ;
    }
}