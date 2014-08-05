<?php
namespace Farpost\WebBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DepartmentType extends AbstractType
{
   public function buildForm(FormBuilderInterface $builder, array $options)
   {
      $get_qb = function () {
         return function($er) {
            return $er->createQueryBuilder('a')->orderBy('a.alias', 'asc');
         };
      };
      $builder->add('id', 'hidden')
              ->add('school', 'entity', [
                  'class' => 'Farpost\StoreBundle\Entity\School',
                  'query_builder' => $get_qb(),
                  'property' => 'alias'
               ])
              ->add('study_type', 'entity', [
                  'class' => 'Farpost\StoreBundle\Entity\StudyType',
                  'query_builder' => $get_qb(),
                  'property' => 'alias'
               ])
              ->add('alias', 'text')
              ->add('save', 'submit', ['label' => 'Сохранить']);
   }

   public function getName()
   {
      return 'school';
   }

   public function setDefaultOptions(OptionsResolverInterface $resolver)
   {
      $resolver->setDefaults([
         'data_class' => 'Farpost\StoreBundle\Entity\Department',
      ]);
   }
}