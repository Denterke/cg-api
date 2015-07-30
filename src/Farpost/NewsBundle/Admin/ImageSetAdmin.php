<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 27/07/15
 * Time: 15:44
 */

namespace Farpost\NewsBundle\Admin;

use Farpost\NewsBundle\Entity\Image;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;

class ImageSetAdmin extends Admin
{
    public function configureFormFields(FormMapper $formMapper)
    {
        if ($this->hasParentFieldDescription()) {
            $getter = 'get' . ucfirst($this->getParentFieldDescription()->getFieldName());
            $parent = $this->getParentFieldDescription()->getAdmin()->getSubject();
            if ($parent) {
                $imageSet = $parent->$getter();
            } else {
                $imageSet = null;
            }
        } else {
            $imageSet = $this->getSubject();
        }

        $fileFieldOptions = [
            'required' => true,
            'label' => 'label.image'
        ];
        if ($imageSet && ($srcImage = $imageSet->getSrcImage()) && ($webPath = $srcImage->getWebPath())) {
            $container = $this->getConfigurationPool()->getContainer();
            $fullPath = $container->get('request')->getBasePath() . '/' . $webPath;
            $fileFieldOptions['help'] = "<img src='$fullPath' class='admin-preview' />";
        }
        $formMapper
            ->add('file', 'file', $fileFieldOptions)
        ;
    }

    public function prePersist($imageSet)
    {
        $this->manageFileUpload($imageSet);
    }

    public function preUpdate($imageSet)
    {
        $this->manageFileUpload($imageSet);
    }

    protected function manageFileUpload($imageSet)
    {
        if ($imageSet->getFile()) {
            $params = [
                [
                    'name' => 'small',
                    'width' => 100,
                    'height' => 100
                ],
                [
                    'name' => 'big',
                    'width' => 200,
                    'height' => 200
                ]
            ];
            $images = $this->getConfigurationPool()->getContainer()->get('farpost_catalogue.image_manager')->createImages($imageSet->getFile(), $params);
            $smallImage = new Image();
            $smallImage->setFile($images['small'])
                ->refreshUpdated()
                ->upload();

            $bigImage = new Image();
            $bigImage->setFile($images['big'])
                ->refreshUpdated()
                ->upload();

            $originalImage = new Image();
            $originalImage->setFile($images['original'])
                ->refreshUpdated()
                ->upload();

            $imageSet->setSmallImage($smallImage)
                ->setBigImage($bigImage)
                ->setSrcImage($originalImage)
            ;
        } else {
            if (!($smallImage = $imageSet->getSmallImage()) || !$smallImage->getFilename()) {
                $imageSet->setSmallImage(null);
            }
            if (!($bigImage = $imageSet->getBigImage()) || !$bigImage->getFilename()) {
                $imageSet->setBigImage(null);
            }
            if (!($originalImage = $imageSet->getSrcImage()) || !$originalImage->getFilename()) {
                $imageSet->setSrcImage(null);
            }
        }
    }

}