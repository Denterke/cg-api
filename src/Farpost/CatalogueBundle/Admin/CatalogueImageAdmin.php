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


class CatalogueImageAdmin extends Admin {
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('file', 'file', [
                'required' => true,
                'label' => 'label.image'
            ])
        ;
    }

    public function prePersist($image)
    {
        $this->manageFileUpload($image);
    }

    public function PreUpdate($image)
    {
        $this->manageFileUpload($image);
    }

    protected function manageFileUpload($image)
    {
        if ($image->getFile()) {
            $image->refreshUpdated();
        }
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
        ;
    }
}