<?php
/**
 * Created by PhpStorm.
 * User: thelightsp
 * Date: 4/28/20
 * Time: 4:24 PM
 */

namespace SM\Checkout\Controller\Index;

use Magento\Braintree\Block\Payment;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use SM\DigitalProduct\Api\Data\DigitalInterface;
use Trans\MasterPayment\Model\MasterPaymentFactory;

/**
 * Multishipping checkout success controller.
 */
class Success extends Action
{
    const NOT_DIGITAL_PRODUCT = 'not_digital_product';
    const DIGITAL_API_FAIL = 'digital_api_fail';
    const CUSTOMER_NUMBER_INVALID = 'customer_number_invalid';
    const PRODUCT_ID_INVALID = 'product_id_invalid';
    const METTER_NUMBER_INVALID = 'metter_number_invalid';
    const OPERATOR_INVALID = 'operator_invalid';
    const TRANSACTION_FAIL = 'transaction_fail';
    const DIGITAL_CHECKOUT_PATH = 'transcheckout/digitalproduct';

    /**
     * @var string[]
     */
    protected $needToPayMethods = [
        'sprint_bca_cc',
        'sprint_bcafullpayment_cc',
        'sprint_allbankfull_cc',
    ];

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Framework\Session\Generic
     */
    protected $session;
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;
    /**
     * @var \SM\Checkout\Model\Payment
     */
    protected $payment;
    /**
     * @var \SM\Checkout\Helper\Payment
     */
    protected $paymentHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \SM\FlashSale\Model\HistoryFactory
     */
    protected $historyFactory;

    /**
     * Success constructor.
     * @param Context                                    $context
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param \Magento\Sales\Model\OrderFactory          $orderFactory
     * @param \SM\Checkout\Model\Payment                 $payment
     * @param \SM\Checkout\Helper\Payment                $paymentHelper
     * @param CheckoutSession                            $checkoutSession
     * @param \Magento\Framework\Session\Generic         $session
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \SM\Checkout\Model\Payment $payment,
        \SM\Checkout\Helper\Payment $paymentHelper,
        CheckoutSession $checkoutSession,
        \Magento\Framework\Session\Generic $session,
        \SM\FlashSale\Model\HistoryFactory $historyFactory,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->resultPageFactory = $pageFactory;
        $this->checkoutSession = $checkoutSession;
        $this->session = $session;
        $this->orderFactory = $orderFactory;
        $this->payment = $payment;
        $this->customerSession = $customerSession;
        $this->historyFactory = $historyFactory;
        parent::__construct($context);
        $this->paymentHelper = $paymentHelper;
    }

    /**
     * Multishipping checkout success page
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     * @throws \Exception
     */
    public function execute()
    {
        /*
         * Hot update for mobile case
         * TODO:  please update again
         */
        $orderId = $this->getRequest()->getParam('orderid');
        if (!empty($orderId)) {
            $response = $this->getApiResponse($orderId);
            echo $response;
            exit();
        }
        $order = $this->checkoutSession->getLastRealOrder();
        if (!$this->checkoutSession->getIsSucceed()&&$this->paymentHelper->isPaymentNeeded($order->getPayment()->getMethod())) {
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Success'));
        $this->session->setOrderIds(null);
        $this->checkoutSession->setIsSucceed(null);

        $flashSaleData = $this->customerSession->getFlashSaleData() ?? [];
        if (!empty($flashSaleData)) {
            foreach ($flashSaleData as $data) {
                $history = $this->historyFactory->create();
                $qtyCustomer = $history->getCollection()
                    ->addFieldToFilter('event_id', $data['event_id'])
                    ->addFieldToFilter('item_id', $data['item_id'])
                    ->addFieldToFilter('customer_id', $this->customerSession->getCustomerId())->getFirstItem();
                if ($qtyCustomer->getId()) {
                    $qtyCustomer->setData('item_qty_purchase',$data['item_qty_purchase']);
                    $qtyCustomer->save();
                }else {
                    $history->setData('event_id', $data['event_id']);
                    $history->setData('item_id', $data['item_id']);
                    $history->setData('customer_id', $this->customerSession->getCustomerId());
                    $history->setData('item_qty_purchase', $data['item_qty_purchase']);
                    $history->save();
                }
            }
        }
        $this->customerSession->setFlashSaleData([]);
        return $resultPage;
    }

    public function getApiResponse($orderId)
    {
        $order = $this->orderFactory->create()->load($orderId);
        if (empty($order->getId())) {
            return 'order not found';
        }
        $res = [
            'status'       => true,
            'message'      => 'payment succeed',
            'order_status' => $order->getStatus()
        ];

        /*
         * Case payment failed
         */
        if ($order && $order->getStatus() == 'pending_payment' && $this->paymentHelper->isPaymentNeeded($order->getPayment()->getMethod())) {
            $this->payment->paymentFailed($order);
            $res['status'] = false;
            $res['message'] = 'payment failed';
            $res['order_status'] = $order->getStatus();
            $res["error_message"] = __("Sorry, we couldn't process your payment. Please select another payment method.");
        }

        return json_encode($res);
    }

}
