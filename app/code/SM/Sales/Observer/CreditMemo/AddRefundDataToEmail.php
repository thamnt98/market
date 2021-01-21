<?php
/**
 * Class AddRefundDataToEmail
 * @package SM\Sales\Observer\CreditMemo
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */


namespace SM\Sales\Observer\CreditMemo;

use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use SM\Sales\Model\Order\IsPaymentMethod;

class AddRefundDataToEmail implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Url
     */
    private $urlBuilder;

    /**
     * AddRefundDataToEmail constructor.
     * @param \Magento\Framework\Url $urlBuilder
     */
    public function __construct(
        \Magento\Framework\Url $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var DataObject $transportObject */
        $transportObject = $observer->getEvent()->getDataByKey('transportObject');
        /** @var Order $order */
        $order = $transportObject->getData("order");
        $paymentMethod = $order->getPayment()->getMethod();
        $isVirtual = IsPaymentMethod::isVirtualAccount($paymentMethod);

        if ($isVirtual) {
            $creditmemo = $transportObject->getData("creditmemo");
            $additionalData = [
                'refund_url' => $this->getRefundUrl(
                    $order->getStoreId(),
                    $creditmemo->getId()
                )
            ];
        } else {
            $additionalData = [
                'refund_url' => ''
            ];
        }
        $additionalData['can_credit_memo'] = $order->canCreditmemo();
        if ($additionalData['can_credit_memo']) {
            $additionalData['items_text'] = '<strong>' . __('some items') . '</strong>';
            $additionalData['transaction_text'] = __('for the unavailable items.');
        } else {
            $additionalData['items_text'] = '<strong>' . __('all items') . '</strong>';
            $additionalData['transaction_text'] = __('for this transaction.');
        }
        $additionalData['is_virtual'] = $isVirtual;
        $additionalData['is_card'] = !$isVirtual;
        $additionalData['is_preauth'] = IsPaymentMethod::isPreAuth($paymentMethod, $order);
        $additionalData['is_debit'] = IsPaymentMethod::isDebitCard($paymentMethod);
        $additionalData['order_increment_id'] = '<strong>' . $order->getIncrementId() . '</strong>';

        $transportObject->setData("additional_data", $additionalData);
    }

    /**
     * @param $storeId
     * @param $creditmemo
     * @return string
     */
    protected function getRefundUrl($storeId, $creditmemo): string
    {
        return $this->urlBuilder->setScope($storeId)->getUrl(
            'sales/creditmemo/requestrefund',
            [
                'creditmemo_id' => $creditmemo
            ]
        );
    }
}
