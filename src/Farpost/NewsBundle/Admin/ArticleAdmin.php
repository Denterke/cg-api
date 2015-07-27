<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 27/07/15
 * Time: 15:20
 */

namespace Farpost\NewsBundle\Admin;


use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ArticleAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title', 'text', [
                'label' => 'label.title'
            ])
            ->add('imageSet', 'sonata_type_admin', [
                'label' => 'label.logo',
                'required' => false,
                'btn_add' => false,
                'btn_delete' => false
            ])
            ->add('body', 'textarea', [
                'label' => 'label.body'
            ])
            ->add('dt', 'sonata_type_datetime_picker', [
                'label' => 'label.datetime',
                'format' => 'dd-MM-YYYY HH:mm'
            ])
            ->add('published', 'checkbox', [
                'label' => 'label.published',
                'required' => false
            ]);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title', null, ['label' => 'label.title'])
            ->add('dt', null, ['label' => 'label.datetime'])
            ->add('published', null, ['label' => 'label.published'])
        ;
    }

    public function preUpdate($article)
    {
        $this->getConfigurationPool()->getAdminByAdminCode('sonata.admin.news_imageset')->preUpdate($article->getImageSet());

        $params = $this->getRequest()->request->get($this->getUniqid());

        if (!isset($params['imageSet']) && !$article->getImageSet()) {
            throw new \Exception('No imageset found');
        }
    }

    public function prePersist($article)
    {
        $this->getConfigurationPool()->getAdminByAdminCode('sonata.admin.news_imageset')->prePersist($article->getImageSet());

        if (!isset($params['imageSet']) && !$article->getImageSet()) {
            throw new \Exception('No imageset found');
        }
    }

}