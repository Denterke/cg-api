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
            ->add('body', 'textarea', [
                'label' => 'label.body'
            ])
            ->add('images', 'sonata_type_collection', [
                'label' => 'label.media',
                'by_reference' => false
            ], [
                'edit' => 'inline',
                'inline' => 'table'
            ])
            ->add('dt', 'datetime', [
                'label' => 'label.datetime',
                'format' => 'dd.MM.YYYY HH:mm',
//                'attr' => [
//                    'data-date-format' => 'dd.MM.YYYY HH:mm'
//                ]
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
        $this->attachImages($article);
    }

    public function prePersist($article)
    {
        $this->attachImages($article);
    }


    public function attachImages($article)
    {
        foreach($article->getImages() as $image) {
            $image->setArticle($article);
        }
    }

}