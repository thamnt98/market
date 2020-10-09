<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Sprint\Plugin\Checkout\Controller\Onepage;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Sales\Model\Order;
use SM\Checkout\Helper\Payment;

class Success
{
    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var mixed
     */
    protected $needToPayMethods = array(
        'sprint_bca_cc',
        'sprint_bcafullpayment_cc',
        'sprint_allbankfull_cc',
        'sprint_mega_cc'
    );
    /**
     * @var \SM\Checkout\Model\Payment
     */
    protected $payment;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;
    /**
     * @var Payment
     */
    protected $paymentHelper;

    /**
     * @param Context $context
     * @param CheckoutSession $checkoutSession
     * @param \SM\Checkout\Model\Payment $payment
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        Context $context,
        CheckoutSession $checkoutSession,
        \SM\Checkout\Model\Payment $payment,
        Payment $paymentHelper,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->checkoutSession = $checkoutSession;
        $this->payment = $payment;
        $this->request = $request;
        $this->paymentHelper = $paymentHelper;
    }

    /**
     * Plugin method
     * @param $subject
     * @param $result
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function afterExecute($subject, $result)
    {
        $order = $this->checkoutSession->getLastRealOrder();
        /*
        *  APO-1926 [API] Checkout - Place orders on mobile
        */
        $orderId = $this->request->getParam('orderid');
        if (!empty($orderId)) {
            return $this->resultRedirectFactory->create()->setPath(
                'transcheckout/index/success?orderid=' . $orderId
            );
        }

        /**
         * Handle Digital success or fail
         * Reference: APO-1418, APO-3123
         */
        if ($order->getIsVirtual()) {
            return $this->handleDigitalSuccess($order);
        }

        return $this->handlePhysicalSuccess($order);
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    private function handleDigitalSuccess(\Magento\Sales\Model\Order $order)
    {
        if ($this->checkOrderIsFailed($order)) {
            /*
             * Re-Active quote when payment failed
             * Reference: APO-2483
             */
            $this->payment->paymentFailed($order);

            /*
             * Redirect to payment page for re-pay
             * Reference: APO-2483
             */
            return $this->resultRedirectFactory->create()->setPath('transcheckout/digitalproduct');
        }

        /**
         * Customer will be redirected to My order screen which show the order with cancelled status
         * Reference APO-3386
         */
        if ($order->getDigitalTransactionFail()) {
            return $this->resultRedirectFactory->create()->setPath('sales/order/digital', array('id' => $order->getId()));
        }

        /*
        * Set checkout session for custom success page
        * Reference: APO-3353
        */
        $this->checkoutSession->setIsSucceed(1);

        /*
        * Redirect to custom success page
        * Reference: APO-1926
        */
        return $this->redirectToSuccessPage();
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    private function handlePhysicalSuccess(\Magento\Sales\Model\Order $order)
    {
        if ($this->checkOrderIsFailed($order)) {
            /*
             * Re-Active quote when payment failed
             * Reference: APO-2483
             */
            $this->payment->paymentFailed($order);

            /*
             * Redirect to payment page for re-pay
             * Reference: APO-2483
             */
            return $this->resultRedirectFactory->create()->setPath('transcheckout/');
        }

        /*
        * Set checkout session for custom success page
        * Reference: APO-3353
        */
        $this->checkoutSession->setIsSucceed(1);

        /*
         * Redirect to custom success page
         * Reference: APO-1926
         */
        return $this->redirectToSuccessPage();
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return bool
     */
    private function checkOrderIsFailed(\Magento\Sales\Model\Order $order)
    {
        return ($order->getStatus() == Order::STATE_CANCELED
                || $order->getStatus() == Order::STATE_PENDING_PAYMENT)
            && $this->paymentHelper->isPaymentNeeded($order);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    private function redirectToSuccessPage()
    {
        return $this->resultRedirectFactory->create()->setPath('transcheckout/index/success');
    }
}
