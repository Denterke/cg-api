<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 30/07/15
 * Time: 13:40
 */

namespace Farpost\CatalogueBundle\Admin;


use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;

class CatalogueCategoryMediaAdmin extends Admin
{
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('media', 'sonata_media_type', [
                'provider' => 'sonata.media.provider.image',
                'context' => 'catalogue',
                'required' => false,
                'auto_initialize' => false
            ])
        ;
    }

}