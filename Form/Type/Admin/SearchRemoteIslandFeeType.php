<?php
namespace Plugin\RemoteIslandFeePlugin\Form\Type\Admin;

use Plugin\RemoteIslandFeePlugin\Repository\RemoteIslandFeeRepository;
use Eccube\Common\EccubeConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

class SearchRemoteIslandFeeType extends AbstractType
{
    /** @var EccubeConfig */
    protected $eccubeConfig;

    /** @var RemoteIslandFeeRepository */
    protected $remoteIslandFeeRepository;

    /**
     * コンストラクタ。
     *
     * @param RemoteIslandFeeRepository $remoteIslandFeeRepository
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(
        RemoteIslandFeeRepository $remoteIslandFeeRepository,
        EccubeConfig $eccubeConfig
    )
    {
        $this->eccubeConfig = $eccubeConfig;
        $this->remoteIslandFeeRepository = $remoteIslandFeeRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // 郵便番号
            ->add(
                'multi', 
                TextType::class,
                [
                    'label' => '郵便番号',
                    'required' => false,
                    'constraints' => [
                        new Length(
                            [
                                'max' => $this->eccubeConfig['eccube_stext_len']
                            ]
                        ),
                    ]
                ]
            );
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_search_fee';
    }    
}