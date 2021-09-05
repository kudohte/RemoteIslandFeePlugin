<?php

namespace Plugin\RemoteIslandFeePlugin\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Eccube\Form\Type\PriceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RemoteIslandFeeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('fee', PriceType::class, [
            'required' => false,
            'attr' => [
                'required' => 'required',
            ]
        ])
        ->add('postal_codes', TextareaType::class, []);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'Plugin\RemoteIslandFeePlugin\Entity\RemoteIslandFee',
            ]
        );
    }

}