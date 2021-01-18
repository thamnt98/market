<?php
/**
 * Class CreditmemoSender
 * @package SM\Sales\Plugin\Model\Order\Email\Sender
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Sales\Plugin\Model\Order\Email\Sender;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Sales\Model\Order;
use SM\Sales\Api\Data\Creditmemo\FormInformationInterface;
use SM\Sales\Model\Creditmemo\RequestFormData;
use SM\Sales\Model\Order\IsPaymentMethod;

class CreditmemoSender
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
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * CardSendJiraTicket constructor.
     * @param \SM\Sales\Model\Creditmemo\SendToJira $sendToJira
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        \SM\Sales\Model\Creditmemo\SendToJira $sendToJira,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        $this->sendToJira = $sendToJira;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param $subject
     * @param $result
     * @param $creditmemo
     * @return bool
     */
    public function afterSend($subject, $result, $creditmemo)
    {
        if ($result === true) {
            return $this->send($creditmemo);
        }

        return $result;
    }

    /**
     * @param $creditmemo
     * @return bool
     */
    protected function send($creditmemo)
    {
        try {
            /** @var Order $order */
            $this->creditmemo = $creditmemo;
            $this->order = $creditmemo->getOrder();

            $paymentMethod = $this->order->getPayment()->getMethod();

            if (IsPaymentMethod::isCard($paymentMethod)
                && $creditmemo->getCreditmemoStatus() != FormInformationInterface::SUBMITTED_VALUE) {
                $data = $this->prepareParams();
                $this->sendToJira->setCustomer($this->customerRepository->getById($this->order->getCustomerId()));
                return $this->sendToJira->send($data);
            }
        } catch (\Exception $e) {
            return false;
        }
        return true;
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
            RequestFormData::TOTAL_REFUND_KEY => (int)$this->creditmemo->getGrandTotal(),
            RequestFormData::CREDITMEMO_ID_KEY => (int)$this->creditmemo->getId(),
            RequestFormData::PAYMENT_METHOD_KEY => $this->order->getPayment()->getMethod(),
        ];
    }
}
