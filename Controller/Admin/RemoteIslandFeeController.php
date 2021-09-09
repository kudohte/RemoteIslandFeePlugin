<?php

namespace Plugin\RemoteIslandFeePlugin\Controller\Admin;

use Plugin\RemoteIslandFeePlugin\Form\Type\Admin\SearchRemoteIslandFeeType;
use Plugin\RemoteIslandFeePlugin\Form\Type\Admin\RemoteIslandFeeType;
use Plugin\RemoteIslandFeePlugin\Repository\RemoteIslandFeeRepository;
use Eccube\Common\Constant;
use Eccube\Repository\Master\PageMaxRepository;
use Knp\Component\Pager\Paginator;
use Eccube\Util\FormUtil;
use Eccube\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class RemoteIslandFeeController extends AbstractController
{
    /** @var string ページ数のセッションキー。 */
    const SESSION_KEY_OF_PAGE_COUNT = 'admin.setting.shop.remote_island_fee_index.search.page_count';

    /** @var string ページ番号のセッションキー。 */
    const SESSION_KEY_OF_PAGE_NO = 'admin.setting.shop.remote_island_fee_index.search.page_no';

    /** @var string 検索条件のセッションキー */
    const SESSION_KEY_OF_SEARCH = 'admin.setting.shop.remote_island_fee_index.search';

    /** @var PageMaxRepository */
    protected $pageMaxRepo;

    /**
     * @var RemoteIslandFeeRepository
     */
    protected $remoteIslandFeeRepository;

    public function __construct(
        PageMaxRepository $pageMaxRepo,
        RemoteIslandFeeRepository $remoteIslandFeeRepository
    ) {
        $this->pageMaxRepo = $pageMaxRepo;
        $this->remoteIslandFeeRepository = $remoteIslandFeeRepository;
    }

    /**
     * @Route("/%eccube_admin_route%/setting/shop/remote_island_fee", name="admin_setting_shop_remote_island_fee")
     * @Route("/%eccube_admin_route%/setting/shop/remote_island_fee/page/{page_no}", requirements={"page_no" = "\d+"}, name="admin_setting_shop_remote_island_fee_page")
     * @Template("@RemoteIslandFeePlugin/admin/remote_island_fee_index.twig")
     * @return array パラメータ。
     */
    public function index(Request $request, $page_no = null, Paginator $paginator)
    {
        $session = $this->session;
        $builder = $this->formFactory->createBuilder(SearchRemoteIslandFeeType::class);

        $searchForm = $builder->getForm();

        $pageMaxis = $this->pageMaxRepo->findAll();
        $pageCount = $session->get(
            self::SESSION_KEY_OF_PAGE_COUNT, 
            $this->eccubeConfig['eccube_default_page_count']
        );
        $pageCountParam = $request->get('page_count');
        if ($pageCountParam && is_numeric($pageCountParam)) {
            foreach ($pageMaxis as $pageMax) {
                if ($pageCountParam == $pageMax->getName()) {
                    $pageCount = $pageMax->getName();
                    $session->set(
                        self::SESSION_KEY_OF_PAGE_COUNT, 
                        $pageCount
                    );
                    break;
                }
            }
        }

        if ('POST' === $request->getMethod()) {
            $searchForm->handleRequest($request);
            if ($searchForm->isValid()) {
                $searchData = $searchForm->getData();
                $page_no = 1;

                $session->set(
                    self::SESSION_KEY_OF_SEARCH, 
                    FormUtil::getViewData($searchForm));
                $session->set(
                    self::SESSION_KEY_OF_PAGE_NO, 
                    $page_no);
            } else {
                return [
                    'searchForm' => $searchForm->createView(),
                    'pagination' => [],
                    'pageMaxis'  => $pageMaxis,
                    'page_no'    => $page_no,
                    'page_count' => $pageCount,
                    'has_errors' => true,
                ];
            }
        } else {
            if (null !== $page_no || $request->get('resume')) {
                if ($page_no) {
                    $session->set(
                        self::SESSION_KEY_OF_PAGE_NO, 
                        (int) $page_no);
                } else {
                    $page_no = $session->get(
                        self::SESSION_KEY_OF_PAGE_NO, 
                        1);
                }

                $viewData = $session->get(
                    self::SESSION_KEY_OF_SEARCH, 
                    []);
            } else {
                $page_no = 1;
                $viewData = FormUtil::getViewData($searchForm);
                $session->set(
                    self::SESSION_KEY_OF_SEARCH, 
                    $viewData);
                $session->set(
                    self::SESSION_KEY_OF_PAGE_NO, 
                    $page_no);
            }

            $searchData = FormUtil::submitAndGetData($searchForm, $viewData);
        }

        /** @var QueryBuilder $qb */
        $qb = $this->remoteIslandFeeRepository->getQueryBuilderBySearchData($searchData);

        $pagination = $paginator->paginate(
            $qb,
            $page_no,
            $pageCount
        );

        return [
            'searchForm' => $searchForm->createView(),
            'pagination' => $pagination,
            'pageMaxis'  => $pageMaxis,
            'page_no'    => $page_no,
            'page_count' => $pageCount,
            'has_errors' => false,
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/setting/shop/remote_island_fee/{id}/delete", requirements={"id" = "\d+"}, name="admin_setting_shop_remote_island_fee_delete", methods={"DELETE"})
     */
    public function delete(Request $request, $id, TranslatorInterface $translator)
    {
        $this->isTokenValid();

        log_info('追加送料削除開始', [$id]);

        $page_no = intval($this->session->get(self::SESSION_KEY_OF_PAGE_NO));
        $page_no = $page_no 
            ? $page_no 
            : Constant::ENABLED;

        $RemoteIslandFee = $this->remoteIslandFeeRepository->find($id);

        if (! $RemoteIslandFee) {
            $this->deleteMessage();

            return $this->redirect(
                $this->generateUrl('admin_setting_shop_remote_island_fee_page',
                [
                    'page_no' => $page_no
                ]
            ).'?resume=' . Constant::ENABLED);
        }

        try {
            $this->entityManager->remove($RemoteIslandFee);
            $this->entityManager->flush($RemoteIslandFee);
            $this->addSuccess('admin.common.delete_complete', 'admin');
        } catch (\Exception $e) {
            log_info('追加送料削除エラー', [$RemoteIslandFee->getId(), $e]);

            $message = trans('admin.common.delete_error');
            $this->addError($message, 'admin');
        }

        log_info('追加送料削除完了', [$id]);

        return $this->redirect(
            $this->generateUrl(
                'admin_setting_shop_remote_island_fee_page',
                [
                    'page_no' => $page_no
                ]
            ).'?resume=' . Constant::ENABLED);
    }
}