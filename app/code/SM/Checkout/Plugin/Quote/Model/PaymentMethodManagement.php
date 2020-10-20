<?php
/**
 * Class PaymentMethodManagement
 * @package SM\Checkout\Plugin\Quote\Model
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Checkout\Plugin\Quote\Model;

use Magento\Framework\Exception\State\InvalidTransitionException;

class PaymentMethodManagement
{
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Payment\Model\Checks\ZeroTotal
     */
    protected $zeroTotalValidator;

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote
     */
    protected $quoteResourceModel;

    /**
     * @var \SM\Checkout\Helper\DigitalProduct
     */
    protected $digitalHelper;

    /**
     * Constructor
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Payment\Model\Checks\ZeroTotal $zeroTotalValidator
     * @param \Magento\Quote\Model\ResourceModel\Quote $quoteResourceModel
     * @param \SM\Checkout\Helper\DigitalProduct $digitalHelper
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Payment\Model\Checks\ZeroTotal $zeroTotalValidator,
        \Magento\Quote\Model\ResourceModel\Quote $quoteResourceModel,
        \SM\Checkout\Helper\DigitalProduct $digitalHelper
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->zeroTotalValidator = $zeroTotalValidator;
        $this->quoteResourceModel = $quoteResourceModel;
        $this->digitalHelper = $digitalHelper;
    }

    /**
     * @param \Magento\Quote\Model\PaymentMethodManagement $subject
     * @param callable $proceed
     * @param $cartId
     * @param $method
     * @return mixed
     * @throws InvalidTransitionException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundSet(
        \Magento\Quote\Model\PaymentMethodManagement $subject,
        callable $proceed,
        $cartId,
        $method
    ) {
        $additionalData = $method->getAdditionalData();
        if ($additionalData && isset($additionalData[0]) && $additionalData[0] == 'digital') {
            return $this->saveDigitalPaymentInformation($cartId, $method);
        }
        return $proceed($cartId, $method);
    }

    /**
     * @param $cartId
     * @param $method
     * @return mixed
     * @throws InvalidTransitionException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function saveDigitalPaymentInformation($cartId, $method)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->get($cartId);
        $this->digitalHelper->setIsDigitalSession(1);
        $quote->setTotalsCollectedFlag(false);
        $method->setChecks([
            \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_CHECKOUT,
            \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_FOR_COUNTRY,
            \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_FOR_CURRENCY,
            \Magento\Payment\Model\Method\AbstractMethod::CHECK_ORDER_TOTAL_MIN_MAX,
        ]);

        $address = $quote->getBillingAddress();

        $paymentData = $method->getData();
        $payment = $quote->getPayment();
        $payment->importData($paymentData);
        $address->setPaymentMethod($payment->getMethod());

        if (!$this->zeroTotalValidator->isApplicable($payment->getMethodInstance(), $quote)) {
            throw new InvalidTransitionException(__('The requested Payment Method is not available.'));
        }

        $this->quoteResourceModel->save($quote);

        return $quote->getPayment()->getId();
    }
}
