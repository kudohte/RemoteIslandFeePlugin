<?php

namespace Plugin\RemoteIslandFeePlugin\Controller\Admin;

use Plugin\RemoteIslandFeePlugin\Entity\RemoteIslandFee;
use Plugin\RemoteIslandFeePlugin\Form\Type\Admin\RemoteIslandFeeType;
use Plugin\RemoteIslandFeePlugin\Repository\RemoteIslandFeeRepository;
use Eccube\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RemoteIslandFeeEditController extends AbstractController
{
    private $remoteIslandFeeRepository;

    public function __construct
    (
        RemoteIslandFeeRepository $remoteIslandFeeRepository
    )
    {
        $this->remoteIslandFeeRepository = $remoteIslandFeeRepository;
    }

    /**
     * @Route("/%eccube_admin_route%/setting/shop/remote_island_fee_edit", name="admin_setting_shop_remote_island_fee_edit")
     * @Template("@RemoteIslandFeePlugin/admin/remote_island_fee_edit.twig")
     */
    public function index(Request $request)
    {
        // 新規登録
        $RemoteIslandFee = new RemoteIslandFee();

        $builder = $this->formFactory
            ->createBuilder(
                RemoteIslandFeeType::class, 
                $RemoteIslandFee);

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            log_info('離島料金登録開始', [$RemoteIslandFee->getId()]);
            $RemoteIslandFee = $form->getData();

            $this->entityManager->persist($RemoteIslandFee);
            $this->entityManager->flush();

            log_info('離島料金登録完了', [$RemoteIslandFee->getId()]);

            $this->addSuccess('admin.common.save_complete', 'admin');

            return $this->redirectToRoute('admin_setting_shop_remote_island_fee');
        }

        return [
            'form' => $form->createView(),
            'RemoteIslandFee' => $RemoteIslandFee,
        ];
    }
}