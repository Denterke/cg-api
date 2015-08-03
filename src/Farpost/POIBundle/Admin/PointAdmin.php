<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 31/07/15
 * Time: 14:13
 */

namespace Farpost\POIBundle\Admin;


use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;

class PointAdmin extends Admin
{

    public function validate(ErrorElement $errorElement, $point)
    {
        if (!$point->getNode() && (!$point->getLevel() || !$point->getLat() || !$point->getLon())) {
            $errorElement->addViolation('error.poi_not_attached');
        }
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('point.tabs.general', [
                'description' => 'description.point.general',
                'translation_domain' => 'FarpostPOIBundle'
            ])
                ->add('label', 'text', [
                    'label' => 'label.label'
                ])
                ->add('content', 'textarea', [
                    'label' => 'label.content',
                    'attr' => [
                        'rows' => 6
                    ]
                ])
                ->add('type', 'sonata_type_model_list', [
                    'label' => 'label.type'
                ])
//                ->add('type', 'sonata_type_model_autocomplete', [
//                    'label' => 'label.type',
//                    'property' => 'name',
//                    'to_string_callback' => function($entity, $property) {
//                        return join(' - ', [$entity->getName(), $entity->getAlias()]);
//                    },
//                    'placeholder' => 'text.start_typing',
//                    'minimum_input_length' => 3
//                ])
            ->add('startAt', 'sonata_type_datetime_picker', [
                    'label' => 'label.startAt',
                    'format' => 'dd-MM-yyyy HH:mm'
                ])
                ->add('endAt', 'sonata_type_datetime_picker', [
                    'label' => 'label.endAt',
                    'format' => 'dd-MM-yyyy HH:mm'
                ])
            ->end()
            ->with('point.tabs.position', [
                'description' => 'description.point.position',
                'translation_domain' => 'FarpostPOIBundle'
            ])
                ->add('lat', null, [
                    'label' => 'label.lat',
                    'required' => false
                ])
                ->add('lon', null, [
                    'label' => 'label.lon',
                    'required' => false
                ])
                ->add('level', 'sonata_type_model_autocomplete', [
                    'label' => 'label.level',
                    'required' => false,
                    'property' => 'alias',
                    'to_string_callback' => function($entity, $property) {
                        return $entity->getAlias();
                    },
                    'placeholder' => 'text.start_typing',
                    'minimum_input_length' => 1
                ])
                ->add('node', 'sonata_type_model_list_with_map', [
                    'label' => 'label.node',
                    'btn_add' => false,
                    'model_manager' => $this->getModelManager(),
                    'class' => $this->getClass(),
                    'btn_map' => 'btn.map'
//                    'btn_list' => 'button.list',
//                    'btn_delete' => 'button.delete'
                ])
            ->end()
        ;
    }

    public function preUpdate($point)
    {
        $point->setVisible(true);
    }

    public function prePersist($point)
    {
        $point->setVisible(true);
    }

    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('label', null, [
                'label' => 'label.label'
            ])
            ->add('type.name', null, [
                'label' => 'label.type'
            ])
            ->add('startAt', null, [
                'label' => 'label.startAt'
            ])
            ->add('endAt', null, [
                'label' => 'label.endAt'
            ])
        ;

    }

    public function configureFiltersFields(DatagridMapper $datagridMapper)
    {

    }

}