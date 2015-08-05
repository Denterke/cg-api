<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 31/07/15
 * Time: 14:13
 */

namespace Farpost\POIBundle\Admin;


use Farpost\POIBundle\Entity\Point;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;

class PointAdmin extends Admin
{

    public function validate(ErrorElement $errorElement, $point)
    {
//        var_dump($point->getNode());
//        $asdf->asdf();
        if (!$point->getNode() && (!$point->getLevel() || !$point->getLat() || !$point->getLon())) {
            $errorElement->addViolation('error.poi_not_attached');
        }
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('point.tabs.general', [
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
                ->add('level', 'sonata_type_model_list', [
                    'label' => 'label.level',
                    'required' => false,
                    'btn_add' => false
                ])
                ->add('node', 'sonata_type_model_list_with_map', [
                    'label' => 'label.node',
                    'btn_add' => false,
                    'model_manager' => $this->getModelManager(),
                    'class' => 'Farpost\MapsBundle\Entity\Node',
                    'btn_map' => 'btn.map'
                ])
            ->end()
        ;

//        var_dump($this->getClass());
//        $asdf->asdf();
    }

    public function preUpdate($point)
    {
        $point->setVisible(true);
        $this->managePosition($point);
    }

    public function prePersist($point)
    {
        $point->setVisible(true);
        $this->managePosition($point);
    }

    /**
     * @param Point $point
     */
    public function managePosition(Point $point)
    {
        if ($node = $point->getNode()) {
            $point->setLat($node->getLat())
                ->setLon($node->getLon())
                ->setLevel($node->getLevel())
            ;
        }
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