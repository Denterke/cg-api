<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 04/08/15
 * Time: 16:47
 */

namespace Farpost\FeedbackBundle\Admin;


use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class FeedbackAdmin extends Admin
{
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
    );

    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('username', null, [
                'label' => 'label.username'
            ])
            ->add('createdAt', null, [
                'label' => 'label.createdAt'
            ])
            ->add('phone', null, [
                'label' => 'label.phone'
            ])
            ->add('message', 'textarea', [
                'label' => 'label.message'
            ])
        ;
    }


    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('username', 'text', [
                'label' => 'label.username'
            ])
            ->add('message', 'textarea', [
                'label' => 'label.message',
                'attr' => [
                    'rows' => 6
                ]
            ])
            ->add('phone', 'text', [
                'label' => 'label.phone'
            ])
            ->add('createdAt', 'text', [
                'label' => 'label.createdAt'
            ])
        ;
    }

    public function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['list', 'show']);
    }

    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('username', null, [
                'label' => 'label.username',
                'route' => [
                    'name' => 'show'
                ]
            ])
            ->add('phone', null, [
                'label' => 'label.phone'
            ])
            ->add('createdAt', null, [
                'label' => 'label.createdAt'
            ])
        ;
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('username', null, [
                'label' => 'label.username'
            ])
            ->add('phone', null, [
                'label' => 'label.phone'
            ])
        ;
    }

}