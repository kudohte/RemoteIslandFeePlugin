<?php

namespace Plugin\RemoteIslandFeePlugin\Service;

use Eccube\Entity\BaseInfo;
use Eccube\Entity\DeliveryFee;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\Order;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Service\PurchaseFlow\ItemHolderPreprocessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Annotation\ShoppingFlow;
use Eccube\Repository\DeliveryFeeRepository;
use Plugin\RemoteIslandFeePlugin\Repository\RemoteIslandFeeRepository;

/**
 * 離島料金判定.
 * 
 * @ShoppingFlow()
 *
 * Class DeliveryFeeFreeByShippingPreprocessor
 * @package Plugin\RemoteIslandFeePlugin\Service\PurchaseFlow\Processor
 */
class DeliveryFeeFreeByShippingPreprocessor implements ItemHolderPreprocessor
{
    /** @var BaseInfo */
    protected $BaseInfo;

    /**
     * @var DeliveryFeeRepository
     */
    protected $deliveryFeeRepository;

    /**
     * @var RemoteIslandFeeRepository
     */
    protected $remoteIslandFeeRepository;

    /**
     * DeliveryFeeFreeByShippingPreprocessor constructor.
     *
     */
    public function __construct(
        BaseInfoRepository $baseInfoRepository,
        DeliveryFeeRepository $deliveryFeeRepository,
        RemoteIslandFeeRepository $remoteIslandFeeRepository
    ) {
        $this->BaseInfo = $baseInfoRepository->get();
        $this->deliveryFeeRepository = $deliveryFeeRepository;
        $this->remoteIslandFeeRepository = $remoteIslandFeeRepository;
    }

    /**
     * @param ItemHolderInterface $itemHolder
     * @param PurchaseContext $context
     *
     * @throws \Doctrine\ORM\NoResultException
     */
    public function process(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        // Orderの場合はお届け先ごとに判定する.
        if ($itemHolder instanceof Order) {
            /** @var Order $Order */
            $Order = $itemHolder;
            foreach ($Order->getShippings() as $Shipping) {
                $isFree = false;
                $total = 0;
                $quantity = 0;
                foreach ($Shipping->getProductOrderItems() as $Item) {
                    $total += $Item->getPriceIncTax() * $Item->getQuantity();
                    $quantity += $Item->getQuantity();
                }
                // 送料無料（金額）を超えている
                if ($this->BaseInfo->getDeliveryFreeAmount()) {
                    if ($total >= $this->BaseInfo->getDeliveryFreeAmount()) {
                        $isFree = true;
                    }
                }
                // 送料無料（個数）を超えている
                if ($this->BaseInfo->getDeliveryFreeQuantity()) {
                    if ($quantity >= $this->BaseInfo->getDeliveryFreeQuantity()) {
                        $isFree = true;
                    }
                }
                // 離島料金判定
                foreach ($Shipping->getOrderItems() as $Item) {
                    if ($Item->getProcessorName() == \Eccube\Service\PurchaseFlow\Processor\DeliveryFeePreprocessor::class) {
                        $postal_code = $Order->getPostalCode()?? NUll;
                        $remoteIslandFee = $this->remoteIslandFeeRepository->getRemoteIslandFee($postal_code);
                        if (!empty($remoteIslandFee)) {
                            // 通常送料判定
                            $DeliveryFee = $this->deliveryFeeRepository->findOneBy([
                                'Delivery' => $Shipping->getDelivery(),
                                'Pref' => $Shipping->getPref(),
                            ]);
                            $delivery_fee = ($isFree === true) ? 0 : $DeliveryFee->getFee();

                            $Item->setPrice($remoteIslandFee->getFee() + $delivery_fee);
                            $Item->setQuantity(1);
                        }
                    }
                }
            }
        }
    }

}