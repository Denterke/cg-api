<?php

namespace Farpost\StoreBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;

class SemesterAdmin extends Admin
{

        protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('id', 'text', [
                'label' => 'Ололо',
                'data'  => '5'
            ])
            ->add('alias')
            ->add('time_start')
            ->add('time_end')
        ;
    }

    public function prePersist($semester)
    {

    }
}