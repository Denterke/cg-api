<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 29/07/15
 * Time: 13:24
 */

namespace Farpost\NewsBundle\Admin;


use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;

class ArticleImageAdmin extends Admin
{
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('media', 'sonata_media_type', [
                'provider' => 'sonata.media.provider.image',
                'context' => 'news',
                'required' => false,
                'empty_on_new' => false
            ])
        ;
    }


}