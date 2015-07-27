<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 09/07/15
 * Time: 14:52
 */

namespace Farpost\CatalogueBundle\Admin;

use Farpost\CatalogueBundle\Entity\CatalogueImage;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Exception\InvalidParameterException;


class CatalogueCategoryAdmin extends Admin
{
    public function configure() {
        $this->setTemplate('edit', 'FarpostCatalogueBundle:CRUD:edit_category.html.twig');
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('is_organization', 'checkbox', [
                'label' => 'label.is_organization',
                'required' => false,
                'help' => 'help.is_organisation'
            ])
            ->add('name', 'text', [
                'label' => 'label.name',
            ])
            ->add('description', 'textarea', [
                'label' => 'label.description',
                'required' => false,
            ])
            ->add('logoStandard', 'sonata_type_admin', [
                'label' => 'label.logo',
                'required' => false,
                'btn_add' => false,
            ])
            ->add('phone', 'text', [
                'label' => 'label.phone',
                'required' => false,
            ])
            ->add('site', 'text', [
                'label' => 'label.site',
                'required' => false,
            ])
            ->add('parents', 'sonata_type_collection', [
                'by_reference' => false,
                'required' => false,
                'label' => 'label.parent_categories',
                'help' => 'help.parent_categories'
            ], [
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'id'
            ])
            ->add('children', 'sonata_type_collection', [
                'by_reference' => false,
                'required' => false,
                'label' => 'label.children_categories',
                'help' => 'help.children_categories'
            ], [
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'id'
            ])
            ->add('objects', 'sonata_type_collection', [
                'by_reference' => false,
                'required' => false,
                'label' => 'label.children_objects',
                'help' => 'help.children_objects'
            ], [
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'id'
            ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('isOrganization', null, ['operator_type' => 'sonata_type_boolean', 'label' => 'label.is_organization'])
            ->add('name', null, ['label' => 'label.name']);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name', null, ['label' => 'label.name'])
            ->add('isOrganization', null, ['label' => 'label.is_organization'])
            ->add('description', null, ['label' => 'label.description'])
            ->add('phone', null, ['label' => 'label.phone'])
            ->add('site', null, ['label' => 'label.site'])
            ->add('id', null, ['label' => 'label.id'])
        ;
    }

    public function prePersist($category)
    {
        $this->manageLogoImageAdmin($category);
    }

    public function preRemove($category)
    {
        if ($category->getIsRoot()) {
            throw new \Exception("Нельзя удалить корневую категорию");
        }
    }

    public function getBatchActions()
    {
        return null;
    }

    public function preUpdate($category)
    {
        $params = $this->getRequest()->request->get($this->getUniqid());

        if (isset($params['logoStandard']) &&
            ($image = $params['logoStandard']) &&
            (isset($image['_delete']) && !empty($image['_delete']))) {
            $category->setLogoStandard(null)
                ->setLogoThumbnail(null)
            ;
        } else {
            $this->manageLogoImageAdmin($category);
        }
    }

    protected function manageLogoImageAdmin($category)
    {
        $image = $category->getLogoStandard();
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

                $category->setLogoStandard($standardImage)
                    ->setLogoThumbnail($thumbnailImage)
                ;
            } elseif (!$image->getFile() && !$image->getFilename()) {
                $category->setLogoStandard(null)
                    ->setLogoThumbnail(null)
                ;
            }
        }
    }
}