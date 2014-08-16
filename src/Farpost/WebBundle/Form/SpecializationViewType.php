<?php
namespace Farpost\WebBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SpecializationViewType extends AbstractType
{
   public function buildForm(FormBuilderInterface $builder, array $options)
   {
      $builder->add('school', 'entity', [
                  'class' => 'Farpost\StoreBundle\Entity\School',
                  'query_builder' => function($er) { return $er->createQueryBuilder('a')->orderBy('a.alias', 'asc'); },
                  'property' => 'alias'
               ])
               ->add('department', 'choice')
               ->add('show', 'submit', ['label' => 'Показать']);
   }

   public function getName()
   {
      return 'specializationsView';
   }

   // public function setDefaultOptions(OptionsResolverInterface $resolver)
   // {
   //    // $resolver->setDefaults([]);
   // }
}
