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
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;


class CatalogueObjectScheduleAdmin extends Admin {
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('dayNumber', 'choice', [
                'choices' => [
                    '1' => 'dow.monday',
                    '2' => 'dow.tuesday',
                    '3' => 'dow.wednesday',
                    '4' => 'dow.thursday',
                    '5' => 'dow.friday',
                    '6' => 'dow.saturday',
                    '7' => 'dow.sunday'
                ],
                'translation_domain' => 'FarpostCatalogueBundle',
                'label' => 'label.day_number'
            ])
            ->add('startAt', 'sonata_type_datetime_picker', [
                'label' => 'label.start_at',
                'format' => 'HH:mm',
            ])
            ->add('endAt', 'sonata_type_datetime_picker', [
                'label' => 'label.end_at',
                'format' => 'HH:mm',
                'date_format' => DateTimeType::DEFAULT_TIME_FORMAT
            ])
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