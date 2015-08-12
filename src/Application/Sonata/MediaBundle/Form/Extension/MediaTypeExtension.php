<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 12/08/15
 * Time: 11:30
 */

namespace Application\Sonata\MediaBundle\Form\Extension;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

class MediaTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'show_unlink' => true
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['show_unlink']) {
            $builder->add('unlink', 'hidden', [
                'mapped' => false,
                'data' => false,
                'required' => false
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'sonata_media_type';
    }
}

}