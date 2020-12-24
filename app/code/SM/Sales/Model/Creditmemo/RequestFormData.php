<?php
/**
 * Class RequestFormData
 * @package SM\Sales\Model\Creditmemo
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Sales\Model\Creditmemo;

class RequestFormData
{
    const ORDER_REFERENCE_NUMBER_KEY = 'reference_number';
    const ORDER_DETAIL_URL_KEY = 'order_url';
    const TOTAL_REFUND_KEY = 'total_refund';
    const CUSTOMER_ID_KEY = 'customer_id';
    const CREDITMEMO_ID_KEY = 'creditmemo_id';
    const PAYMENT_NUMBER_KEY = 'payment_number';
    const BANK_KEY = 'bank';
    const ACCOUNT_KEY = 'account_no';
    const ACCOUNT_NAME_KEY = 'account_name';
    const PAYMENT_METHOD_KEY = 'payment_method';
    const CREDITMEMO_KEY = 'creditmemo';

    /**
     * @var array
     */
    private $formData;

    /**
     * @var \Magento\Sales\Api\CreditmemoRepositoryInterface
     */
    private $creditmemoRepository;

    /**
     * @var \Magento\Sales\Model\Order
     */
    private $order;

    /**
     * @var \Magento\Sales\Api\Data\CreditmemoInterface
     */
    private $creditmemo;

    /**
     * RequestFormData constructor.
     * @param \Magento\Sales\Api\CreditmemoRepositoryInterface $creditmemoRepository
     */
    public function __construct(
        \Magento\Sales\Api\CreditmemoRepositoryInterface $creditmemoRepository
    ) {
        $this->creditmemoRepository = $creditmemoRepository;
    }

    /**
     * @param $creditmemoId
     * @return bool
     */
    public function setFormData($creditmemoId): bool
    {
        if (empty($this->formData)) {
            try {
                $this->creditmemo = $creditmemo = $this->creditmemoRepository->get($creditmemoId);
            } catch (\Exception $e) {
                return false;
            }
            $this->order = $order = $creditmemo->getOrder();

            $this->formData =  [
                self::ORDER_REFERENCE_NUMBER_KEY => $order->getData('reference_number'),
                self::TOTAL_REFUND_KEY => $creditmemo->getGrandTotal(),
                self::CUSTOMER_ID_KEY => $order->getCustomerId(),
                self::CREDITMEMO_ID_KEY => $creditmemoId,
                self::PAYMENT_NUMBER_KEY => $order->getData('reference_payment_number')
            ];
        }

        return true;
    }

    /**
     * @param $currentCustomerId
     * @return bool
     */
    public function validateCustomer($currentCustomerId): bool
    {
        $customerId = $this->getCustomerId();
        return $customerId == $currentCustomerId;
    }

    /**
     * @return array
     */
    public function getFormData(): array
    {
        return $this->formData;
    }

    /**
     * @return int
     */
    public function getTotalRefund(): int
    {
        return $this->formData[self::TOTAL_REFUND_KEY];
    }

    /**
     * @return string
     */
    public function getReferenceNumber(): string
    {
        return $this->formData[self::ORDER_REFERENCE_NUMBER_KEY];
    }

    /**
     * @return string
     */
    public function getPaymentNumber(): string
    {
        return $this->formData[self::PAYMENT_NUMBER_KEY];
    }

    /**
     * @return string
     */
    public function getOrderUrl(): string
    {
        return $this->formData[self::ORDER_DETAIL_URL_KEY];
    }

    /**
     * @return string
     */
    public function getCustomerId(): string
    {
        return $this->formData[self::CUSTOMER_ID_KEY];
    }

    /**
     * @return string
     */
    public function getCreditmemoId(): string
    {
        return $this->formData[self::CREDITMEMO_ID_KEY];
    }

    /**
     * @return \Magento\Sales\Api\Data\CreditmemoInterface
     */
    public function getCreditmemo(): \Magento\Sales\Api\Data\CreditmemoInterface
    {
        return $this->creditmemo;
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder(): \Magento\Sales\Model\Order
    {
        return $this->order;
    }
}
