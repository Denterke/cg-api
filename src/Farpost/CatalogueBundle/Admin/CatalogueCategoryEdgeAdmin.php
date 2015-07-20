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
        $showFields = [
            'parent' => true,
            'child' => true
        ];
        if ($this->hasParentFieldDescription()) {
            $parentFieldName = $this->getParentFieldDescription()->getFieldName();
            if ($parentFieldName === 'parents' || $parentFieldName === 'categories') {
                $showFields['child'] = false;
            } else {
                $showFields['parent'] = false;
            }
        }
        foreach($showFields as $field => $show) {
            if (!$show) {
                continue;
            }
            $formMapper->add($field, 'sonata_type_model', ['label' => "label.{$field}_category"]);
        }
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('parent')
            ->add('child')
        ;
    }

    public function assertNoCycles($edge)
    {
        $hasCycles = $this->getConfigurationPool()->getContainer()->get('doctrine')
            ->getRepository('FarpostCatalogueBundle:CatalogueCategoryEdge')
            ->hasCycles($edge);
        if ($hasCycles) {
            throw new \Exception('Has cycles!');
        }
    }

    public function prePersist($edge)
    {
//        $this->assertNoCycles($edge);
    }

    public function preUpdate($edge)
    {
//        $this->assertNoCycles($edge);
    }
}