<?php
/**
 * Class CardSendJiraTicket
 * @package SM\Sales\Observer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\Sales\Observer\CreditMemo;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use SM\Sales\Model\Creditmemo\RequestFormData;
use SM\Sales\Model\Order\IsPaymentMethod;

class CardSendJiraTicket implements ObserverInterface
{
    /**
     * @var \Magento\Sales\Model\Order\Creditmemo
     */
    private $creditmemo;

    /**
     * @var \Magento\Sales\Model\Order
     */
    private $order;

    /**
     * @var \SM\Sales\Model\Creditmemo\SendToJira
     */
    private $sendToJira;

    /**
     * CardSendJiraTicket constructor.
     * @param \SM\Sales\Model\Creditmemo\SendToJira $sendToJira
     */
    public function __construct(
        \SM\Sales\Model\Creditmemo\SendToJira $sendToJira
    ) {
        $this->sendToJira = $sendToJira;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        $this->creditmemo = $observer->getEvent()->getCreditmemo();
        $this->order = $this->creditmemo->getOrder();
        $paymentMethod = $this->order->getPayment()->getMethod();

        if (IsPaymentMethod::isCard($paymentMethod)) {
            $data = $this->prepareParams();
            $this->sendToJira->send($data);
        }
    }

    /**
     * @return array
     */
    protected function prepareParams(): array
    {
        return [
            RequestFormData::BANK_KEY => '',
            RequestFormData::ACCOUNT_NAME_KEY => '',
            RequestFormData::ACCOUNT_KEY => '',
            RequestFormData::ORDER_REFERENCE_NUMBER_KEY => $this->order->getData('reference_order_id'),
            RequestFormData::PAYMENT_NUMBER_KEY => $this->order->getData('reference_payment_number'),
            RequestFormData::TOTAL_REFUND_KEY => (int) $this->creditmemo->getGrandTotal(),
            RequestFormData::CREDITMEMO_ID_KEY => (int)$this->creditmemo->getId(),
            RequestFormData::PAYMENT_METHOD_KEY => $this->order->getPayment()->getMethod(),
        ];
    }
}
