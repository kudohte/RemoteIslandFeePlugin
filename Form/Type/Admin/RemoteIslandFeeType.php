<?php

namespace Plugin\RemoteIslandFeePlugin\Form\Type\Admin;

use Plugin\RemoteIslandFeePlugin\Entity\RemoteIslandFee;
use Plugin\RemoteIslandFeePlugin\Repository\RemoteIslandFeeRepository;
use Symfony\Component\Form\AbstractType;
use Eccube\Form\Type\PriceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RemoteIslandFeeType extends AbstractType
{
    /**
     * @var RemoteIslandFeeRepository
     */
    protected $remoteIslandFeeRepository;

    /**
     * RemoteIslandFeeType constructor.
     *
     * @param RemoteIslandFeeRepository $remoteIslandFeeRepository
     */
    public function __construct(
        RemoteIslandFeeRepository $remoteIslandFeeRepository
    ) {
        $this->remoteIslandFeeRepository = $remoteIslandFeeRepository;
    }

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
        ->add('postal_code', TextType::class, []);

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();

            /** @var RemoteIslandFee $RemoteIslandFee */
            $RemoteIslandFee = $event->getData();

            $count = $this->remoteIslandFeeRepository
                ->createQueryBuilder('r')
                ->select('COUNT(r)')
                ->where('r.postal_code = :postal_code')
                ->setParameter('postal_code', $RemoteIslandFee->getPostalCode())
                ->getQuery()
                ->getSingleScalarResult();

            if ($count > 0) {
                $form['postal_code']->addError(new FormError(trans('admin.setting.shop.postal_code_exists')));
            }
        });
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