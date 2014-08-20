<?php
namespace Farpost\WebBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GeoBases extends AbstractType
{
   public function buildForm(FormBuilderInterface $builder, array $options)
   {
      $builder->add('id', 'hidden')
              ->add('alias', 'text')
              ->add('date', 'text')
              ->add('upload', 'submit', ['label' => 'Сохранить']);
   }

   public function getName()
   {
      return 'school';
   }

   public function setDefaultOptions(OptionsResolverInterface $resolver)
   {
      $resolver->setDefaults([
         'data_class' => 'Farpost\StoreBundle\Entity\School',
      ]);
   }
}