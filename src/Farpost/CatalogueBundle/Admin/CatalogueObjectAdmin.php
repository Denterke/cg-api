<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 09/07/15
 * Time: 12:25
 */

namespace Farpost\CatalogueBundle\Admin;

use Farpost\CatalogueBundle\Entity\CatalogueImage;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class CatalogueObjectAdmin extends Admin
{
    protected $formOptions = [
//        'cascade_validation' => true
    ];

    public function configure() {
        $this->setTemplate('edit', 'FarpostCatalogueBundle:CRUD:edit_object.html.twig');
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', 'text', ['label' => 'label.name'])
            ->add('description', 'textarea', [
                'label' => 'label.description',
                'required' => false,
                'attr' => [
                    'rows' => 6
                ]
            ])
            ->add('logoStandard', 'sonata_type_admin', [
                'label' => 'label.logo',
                'required' => false,
                'btn_add' => false
            ])
            ->add('phone', 'text', [
                'label' => 'label.phone',
                'required' => false
            ])
            ->add('site', 'text', [
                'label' => 'label.site',
                'required' => false
            ])
            ->add('categories', 'sonata_type_collection', [
                'by_reference' => false,
                'required' => false,
                'label' => 'label.parent_categories',
                'help' => 'help.parent_categories'
            ], [
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'id'
            ])
            ->add('schedule', 'sonata_type_collection', [
                'by_reference' => false,
                'required' => false,
                'label' => 'label.schedule'
            ], [
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'id'
            ])
            ->add('images', 'sonata_type_collection', [
                'label' => 'label.media',
                'by_reference' => false
            ], [
                'edit' => 'inline',
                'inline' => 'table'
            ])
            ->add('node', 'sonata_type_model_list_with_map', [
                'label' => 'label.node',
                'btn_add' => false,
                'model_manager' => $this->getModelManager(),
                'class' => 'Farpost\MapsBundle\Entity\Node',
                'btn_map' => 'btn.map'
            ])
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, ['label' => 'label.name'])
            ->add('without_node', 'doctrine_orm_callback', [
                'label' => 'label.without_node',
                'callback' => function($queryBuilder, $alias, $field, $value) {
                    if (!$value['value']) {
                        return;
                    }
                    $queryBuilder->andWhere(sprintf('%s.node is NULL', $alias));

                    return true;
                },
                'field_type' => 'checkbox'
            ])
            ->add('strange', 'doctrine_orm_callback', [
                'label' => 'label.strange',
                'callback' => function($queryBuilder, $alias, $field, $value) {
                    if (!$value['value']) {
                        return;
                    }
                    $queryBuilder->andWhere(sprintf('%s.node is NULL', $alias))
                        ->andWhere(sprintf('%s.description is NULL', $alias))
                        ->andWhere(sprintf('LENGTH(%s.name) <= 5', $alias))
                        ->andWhere(sprintf('%s.phone is NULL', $alias))
                        ->andWhere(sprintf('%s.site is NULL', $alias))
                    ;
                    return true;
                },
                'field_type' => 'checkbox'
            ])
            ->add('without_parents', 'doctrine_orm_callback', [
                'label' => 'label.without_parents',
                'callback' => function($queryBuilder, $alias, $field, $value) {
                    if (!$value['value']) {
                        return;
                    }
                    $queryBuilder->andWhere(sprintf('SIZE(%s.categories) = 0', $alias));

                    return true;
                },
                'field_type' => 'checkbox'
            ])
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name', null, ['label' => 'label.name'])
            ->add('description', null, ['label' => 'label.description'])
            ->add('phone', null, ['label' => 'label.phone'])
            ->add('site', null, ['label' => 'label.site'])
            ->add('id', null, ['label' => 'label.id'])
        ;
    }

    public function prePersist($object)
    {
        $this->manageLogoImageAdmin($object);
//        $this->manageNode($object);
    }

    public function preUpdate($object)
    {
//        $this->manageNode($object);
        $params = $this->getRequest()->request->get($this->getUniqid());

        if (isset($params['logoStandard']) &&
        ($image = $params['logoStandard']) &&
        (isset($image['_delete']) && !empty($image['_delete']))) {
            $object->setLogoStandard(null);
            $object->setLogoThumbnail(null);
        } else {
            $this->manageLogoImageAdmin($object);
        }
    }

    protected function manageNode($object)
    {
//        $params = $this->getRequest()->request->get($this->getUniqid());
//        if (!$object->getNode() || isset($params['node']['_delete']) || !$object->getNode()->getId()) {
//            $object->setNode(null);
//            return;
//        }
//        $nodeId = $object->getNode()->getId();//intval($params['node']);
//        $em = $this->getModelManager()->getEntityManager($this->getSubject());
//        $node = $em->getRepository('FarpostMapsBundle:Node')->findOneById($nodeId);
//
//        if (!$node) {
//            $object->setNode(null);
//            return;
//        }
//        $object->setNode($node);
    }

    protected function manageLogoImageAdmin($object)
    {
        $image = $object->getLogoStandard();
        if ($image) {
            if ($image->getFile()) {
                $params = [
                    [
                        'name' => 'thumbnail',
                        'width' => 100,
                        'height' => 100
                    ]
                ];
                $images = $this->getConfigurationPool()
                    ->getContainer()
                    ->get('farpost_catalogue.image_manager')
                    ->createImages($image->getFile(), $params);
                $standardImage = new CatalogueImage();
                $standardImage->setFile($images['original'])
                    ->refreshUpdated();

                $thumbnailImage = new CatalogueImage();
                $thumbnailImage->setFile($images['thumbnail'])
                    ->refreshUpdated();

                $object->setLogoStandard($standardImage)
                    ->setLogoThumbnail($thumbnailImage)
                ;
            } elseif (!$image->getFile() && !$image->getFilename()) {
                $object->setLogoStandard(null)
                    ->setLogoThumbnail(null)
                ;
            }
        }
    }
}