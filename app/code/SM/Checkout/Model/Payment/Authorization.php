<?php


namespace SM\Checkout\Model\Payment;


use Magento\Checkout\CustomerData\Cart;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Quote\Model\QuoteRepository;
use SM\Checkout\Helper\Payment;
use Trans\Sprint\Helper\Config;
use Trans\Sprint\Helper\Data;

class Authorization
{
    /**
     * @var Magento\Checkout\Model\Session
     */
    protected $session;

    /**
     * @var Magento\Checkout\CustomerData\Cart
     */
    protected $cart;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $salesOrder;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $remoteAddress;

    /**
     * @var \Trans\DokuPayment\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var UrlBuilder
     */
    protected $urlBuilder;

    /**
     * @var \Trans\Sprint\Api\Data\SprintResponseInterfaceFactory
     */
    protected $sprintResponse;

    /**
     * @var \Trans\Sprint\Api\SprintResponseRepositoryInterface
     */
    protected $sprintRepository;

    /**
     * @var \Trans\Sprint\Helper\SalesOrder
     */
    protected $orderHelper;
    /**
     * @var QuoteRepository
     */
    protected $quoteRepository;
    /**
     * @var Payment
     */
    protected $paymentHelper;
    /**
     * @var \Trans\Sprint\Model\ResourceModel\SprintResponse
     */
    protected $sprintResource;

    /**
     * @param \Magento\Framework\App\Action\Context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
     * @param \Magento\Sales\Model\Order $salesOrder
     * @param Session $session
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     * @param \Trans\DokuPayment\Helper\Data $dataHelper
     * @param \Trans\Sprint\Api\Data\SprintResponseInterfaceFactory $sprintResponse
     * @param \Trans\Sprint\Api\SprintResponseRepositoryInterface $sprintResRepository
     * @param \Trans\Sprint\Helper\SalesOrder $orderHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Sales\Model\Order $salesOrder,
        Session $session,
        Data $dataHelper,
        \Trans\Sprint\Api\Data\SprintResponseInterfaceFactory $sprintResponse,
        \Trans\Sprint\Api\SprintResponseRepositoryInterface $sprintRepository,
        \Trans\Sprint\Helper\SalesOrder $orderHelper,
        QuoteRepository $quoteRepository,
        \Trans\Sprint\Model\ResourceModel\SprintResponse $sprintResource,
        Payment $paymentHelper,
        \Magento\Payment\Model\Config $paymentConfig
    ) {

        $this->resultJsonFactory = $resultJsonFactory;
        $this->salesOrder = $salesOrder;
        $this->session = $session;
        $this->dataHelper = $dataHelper;
        $this->sprintResponse = $sprintResponse;
        $this->sprintRepository = $sprintRepository;
        $this->paymentConfig = $paymentConfig;
        $this->orderHelper = $orderHelper;
        $this->quoteRepository = $quoteRepository;

        $this->config = $this->dataHelper->getConfigHelper();
        $this->logger = $this->dataHelper->getLogger();
        $this->urlBuilder = $this->dataHelper->getUrlBuilder();
        $this->paymentHelper = $paymentHelper;
        $this->sprintResource = $sprintResource;
    }

    /**
     * Execute insert transaction to sprint asia API
     *
     * @return json
     */
    public function sendOrderToPaymentGateway($orderId)
    {
        $this->logger->info('===== Mobile Authorization Controller ===== Start');

        $checkoutOrder = $this->salesOrder->load($orderId);

        $cardTokenUse = 'CREATETOKEN';
        $cardToken = '';


        $orderIncrementId = $checkoutOrder->getIncrementId();
        $expire = $this->config->getExpiry();

        $order = $this->salesOrder->loadByIncrementId($orderIncrementId);
        $this->logger->info('additional information = ' . $this->dataHelper->serializeJson($order->getPayment()->getData()));

        $paymentMethod = $order->getPayment()->getMethod();
        $paymentAdditionalInformation = $order->getPayment()->getAdditionalInformation();

        $refNumber = $this->sprintResource->getReferenceNumber($orderIncrementId);
        $this->logger->info('reference_number = ' . $refNumber);

        $transactionNo = $refNumber ? $refNumber : $orderIncrementId;
        $grandTotal = $order->getGrandTotal();
        $serviceFee = $this->getServiceFee($order);
        if ($refNumber) {
            $subOrder = $this->orderHelper->getSubOrders($refNumber);
            $grandTotal = $this->orderHelper->getSubOrdersGrandTotal($subOrder);
            $serviceFee = $this->orderHelper->getSubOrdersServiceFee($subOrder);
        }

        $billingData = $order->getBillingAddress()->getData();
        $this->logger->info('Billing data = ' . $this->dataHelper->serializeJson($billingData));

        $orderDate = $this->dataHelper->convertDatetime($order->getCreatedAt());

        $dataPayment['channelId']           = $this->getChannelId($order);
        $dataPayment['currency']            = Data::CURRENCY;
        $dataPayment['transactionNo']       = $transactionNo;
        $dataPayment['transactionAmount']   = round($grandTotal, 2);
        $dataPayment['transactionFee']      = $serviceFee;
        $dataPayment['transactionDate']     = $orderDate;
        $dataPayment['transactionExpire']   = date('Y-m-d H:i:s', strtotime('+' . $expire . ' minutes', strtotime($dataPayment['transactionDate'])));
        $dataPayment['callbackURL']         = $this->urlBuilder->getUrl('checkout/onepage/success/').'?orderid='.$orderId;
        $dataPayment['description']         = $this->dataHelper->getDescription($order);
        $dataPayment['customerAccount']     = $this->getCustomerAccount($order, $paymentMethod);
        $dataPayment['customerName']        = $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname();
        $dataPayment['customerPhone']       = $billingData['telephone'];
        $dataPayment['customerEmail']       = $order->getCustomerEmail();
        $dataPayment['customerBillAddress'] = $billingData['street'] . ' ' . isset($billingData['district']);
        $dataPayment['customerBillCity']    = $billingData['city'];
        $dataPayment['customerBillState']   = $billingData['region'];
        $dataPayment['customerBillZipCode'] = $billingData['postcode'];
        $dataPayment['customerBillCountry'] = 'ID';
        $dataPayment['customerIp']          = $order->getRemoteIp();
        $dataPayment['authCode']            = $this->dataHelper->doAuthCode(array('channel_id' => $dataPayment['channelId'], 'transaction_no' => $dataPayment['transactionNo'], 'transaction_amount' => $dataPayment['transactionAmount']), $paymentMethod);
        $dataPayment['cardTokenUse']        = $cardTokenUse;
        $dataPayment['cardToken']        	= $cardToken;
        $dataPayment['serviceCode']         = $this->config->getPaymentChannelServicecode($paymentMethod);
        $dataPayment['transactionFeature']  = $order->getSprintTermChannelid() !== null ? $this->dataHelper->serializeJson(array('tenor' => $order->getSprintTermChannelid())) : array();

        $this->logger->info('Data Payment = ' . $this->dataHelper->serializeJson($dataPayment));

        $hit = $this->hitApi($dataPayment, $paymentMethod);

        if ($hit != false) {
            $counter = 1;
            while ($hit['insertStatus'] != '00' && strpos($hit['insertMessage'], 'Exist') !== false) {
                $dataPayment['transactionNo'] = $orderIncrementId . '-' . $counter;
                $dataPayment['authCode']      = $this->dataHelper->doAuthCode(array('channel_id' => $dataPayment['channelId'], 'transaction_no' => $dataPayment['transactionNo'], 'transaction_amount' => $dataPayment['transactionAmount']), $paymentMethod);
                $order->setSprintTransactionNo($dataPayment['transactionNo']);
                $order->save();
                $hit = $this->hitApi($dataPayment, $paymentMethod);
                $counter++;
            }
            if ($hit['insertStatus'] == '00') {
                $this->session->setIsRedirectedToSprint(true);
            }
            $this->saveResponse($order, $dataPayment, $hit);
        }

        $this->logger->info('===== Mobile Authorization Controller ===== End');
        $response = $hit;
        if ($this->paymentHelper->isVirtualAccount($paymentMethod)){
            $response['account_number'] = $dataPayment['customerAccount'];
            $response['expired_time'] = $dataPayment['transactionExpire'];
            $response['total_amount'] = $dataPayment['transactionAmount'];
            $response['reference_number'] = $dataPayment['transactionNo'];
        }
        return $response;

    }

    /**
     * Get service fee
     *
     * @param Magento\Sales\Api\Data\OrderInterface $order
     * @return int
     */
    protected function getServiceFee($order)
    {
        $serviceFee = 0;

        if($order->getData('service_fee')) {
            $serviceFee = $order->getData('service_fee');
        }

        return (int)$serviceFee;
    }

    /**
     * Hit API and retry 3 times if timeout
     *
     * @param array $dataPayment
     * @param string $paymentMethod
     * @return array | bool
     */
    protected function hitApi($dataPayment, $paymentMethod) {
        $try = 1;
        $hit = false;
        while ($hit == false && $try <= 3) {
            $this->logger->info('Try hit API ' . $try);
            $this->logger->info('Data Payment = ' . $this->dataHelper->serializeJson($dataPayment));
            $hit = $this->post($dataPayment, Config::PAYMENT_REGISTER_URL, $paymentMethod);
            $this->logger->info('$hit = ' . $this->dataHelper->serializeJson($hit));
            $try++;
        }

        return $hit;
    }

    /**
     * Save API response
     *
     * @param Magento\Sales\Model\Order $order
     * @param json $response
     */
    protected function saveResponse($order, $dataPayment, $response) {
        $this->logger->info('Save Response Start');
        $orderIncrementId = $order->getIncrementId();
        $sprintResponse   = $this->sprintRepository->getByTransactionNo($orderIncrementId);

        $this->logger->info($this->dataHelper->serializeJson($response));

        if ($order && $response) {
            try {
                $data['store_id']         = $this->session->getQuote()->getStoreId();
                $data['quote_id']         = $order->getQuoteId();
                $data['channel_id']       = $this->getChannelId($order);
                $data['transaction_no'] = $dataPayment['transactionNo'];
                $data['currency']         = $response['currency'];
                $data['insert_status']    = $response['insertStatus'];
                $data['insert_message']   = $response['insertMessage'];
                $data['insert_id']        = isset($response['insertId']) ? $response['insertId'] : '';
                $data['redirect_url']     = isset($response['redirectURL']) ? $response['redirectURL'] : null;
                $data['redirect_data']    = isset($response['redirectData']) ? json_encode($response['redirectData']) : null;
                $data['additional_data']  = isset($response['additionalData']) ? $this->dataHelper->serializeJson($response['additionalData']) : null;
                $data['payment_method']   = $order->getPayment()->getMethod();
                $data['customer_account'] = $dataPayment['customerAccount'];
                $data['insert_date']      = $dataPayment['transactionDate'];
                $data['expire_date']      = $dataPayment['transactionExpire'];
                $data['flag']             = 'pending';
                $this->logger->info('Data to save : ' . $this->dataHelper->serializeJson($data));
                $sprintResponse->addData($data);
                $this->sprintRepository->save($sprintResponse);
            } catch (\Exception $e) {
                $this->logger->info('Generate error ' . $e->getMessage());
                return false;
            }
        }

        $this->logger->info('Save Response End');
        return true;
    }

    /**
     * Get payment channel id
     *
     * @param \Magento\Sales\Model\Order $order
     * @return string
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function getChannelId($order) {
        $payment       = $order->getPayment();
        $paymentMethod = $payment->getMethod();
        $this->logger->info('$paymentMethod = ' . $paymentMethod);

        $channel = $this->config->getPaymentChannel($paymentMethod);
        $this->logger->info('$channel = ' . $channel);

        $condition = $channel === Config::CREDIT_CARD_CHANNEL;

        switch ($condition) {
            case true:
                $term = $order->getSprintTermChannelid();
                if (!$term) {
                    $additionalInformation = $payment->getAdditionalInformation();
                    if (isset($additionalInformation['tenor'])) {$term = $additionalInformation['tenor'];}
                }
                if (!$term) {
                    $quoteId=$order->getQuoteId();
                    $term = $this->quoteRepository->get($quoteId)->getSprintTermChannelid();
                }

                $installmentTerm = $this->config->getInstallmentTerm($paymentMethod);
                $terms           = $this->dataHelper->unserializeJson($installmentTerm);

                $channelId = $this->config->getPaymentChannelId($paymentMethod);
                if (!empty($terms) && is_array($terms)) {
                    foreach ($terms as $key => $value) {

                        if ($value['term'] === $term) {

                            $channelId = $value['channelId'];
                        }
                    }
                }

                break;

            case false:
                $channelId = $this->config->getPaymentChannelId($paymentMethod);
                break;
        }

        $this->logger->info('$channelId = ' . $channelId);
        return $channelId;
    }

    protected function getCustomerAccount($order, $paymentMethod) {
        $channel         = $this->config->getPaymentChannel($paymentMethod);
        $customerAccount = substr(str_replace(' ', '', $order->getCustomerId() . $order->getCustomerFirstname() . $order->getCustomerLastname()), 0, 50);

        if ($channel === Config::VIRTUAL_ACCOUNT_CHANNEL) {
            $customerAccount = $this->dataHelper->generateCustomerAccount($paymentMethod, $order->getCustomerId());
        }

        return $customerAccount;
    }

    /**
     * [createCsrfValidationException description]
     * @param  RequestInterface $request
     * @return mixing
     */
    public function createCsrfValidationException(RequestInterface $request):  ? InvalidRequestException {
        return null;
    }

    /**
     * [validateForCsrf description]
     * @param  RequestInterface $request
     * @return boolean
     */
    public function validateForCsrf(RequestInterface $request) :  ? bool {
        return true;
    }

    public function post($dataPayment, $action, $paymentMethod)
    {
        $this->logger->info('===== ' . $action . ' Mobile ===== Start');

        $url = $this->config->getApiUrl($action, $paymentMethod);
        $this->logger->info('URL = ' . $url);
        try {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            http_build_query($dataPayment) );

        // In real life you should use something like:
        // curl_setopt($ch, CURLOPT_POSTFIELDS,
        //          http_build_query(array('postvar1' => 'value1')));

        // Receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close ($ch);
        } catch (\Exception $e) {
            $this->logger->info('HIT API ERROR = ' . $e->getMessage());
            return false;
        }
        $responseJson = $server_output;

        if (is_string($responseJson)) {
            $responseJson = $this->dataHelper->unserializeJson($responseJson);
        }

        $this->logger->info('RESPONSE = ' . $this->dataHelper->serializeJson($responseJson));

        $this->logger->info('===== ' . $action . ' Mobile ===== End');

        return $responseJson;

    }

}
