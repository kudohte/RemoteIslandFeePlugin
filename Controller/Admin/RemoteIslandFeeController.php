<?php

namespace Plugin\RemoteIslandFeePlugin\Controller\Admin;

use Plugin\RemoteIslandFeePlugin\Form\Type\Admin\RemoteIslandFeeType;
use Plugin\RemoteIslandFeePlugin\Repository\RemoteIslandFeeRepository;
use Eccube\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RemoteIslandFeeController extends AbstractController
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
     * @Route("/%eccube_admin_route%/setting/shop/remote_island_fee", name="admin_setting_shop_remote_island_fee")
     * @Template("@RemoteIslandFeePlugin/admin/remote_island_fee.twig")
     */
    public function index(Request $request)
    {
        $RemoteIslandFee = $this->remoteIslandFeeRepository->find(1);

        $builder = $this->formFactory->createBuilder(RemoteIslandFeeType::class,$RemoteIslandFee);

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() &&  $form->isValid()) {
            log_info('離島料金編集開始');
            $RemoteIslandFee = $form->getData();

            log_info('離島料金登録開始');
            try {
                $this->entityManager->persist($RemoteIslandFee);
                $this->entityManager->flush($RemoteIslandFee);
                log_info('離島料金登録完了');
            } catch(\Exception $e) {
                log_error('離島料金登録エラー',[
                    'err' => $e
                ]);
            }

            $this->addSuccess('admin.common.save_complete', 'admin');

            return $this->redirectToRoute('admin_setting_shop_remote_island_fee');

        }

        return [
            'form' => $form->createView(),
            'RemoteIslandFee' => $RemoteIslandFee,
        ];
    }

}