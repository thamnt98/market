<?php
/**
 * Class PaymentSuccessUpdateBefore
 * @package SM\Checkout\Observer\DigitalProduct
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */


namespace SM\Checkout\Observer\DigitalProduct;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use SM\DigitalProduct\Api\Data\DigitalInterface;
use Magento\Sales\Model\Order;

class PaymentSuccessUpdateBefore implements ObserverInterface
{
    const NOT_DIGITAL_PRODUCT = 'not_digital_product';
    const DIGITAL_API_FAIL = 'digital_api_fail';
    const CUSTOMER_NUMBER_INVALID = 'customer_number_invalid';
    const PRODUCT_ID_INVALID = 'product_id_invalid';
    const METER_NUMBER_INVALID = 'meter_number_invalid';
    const OPERATOR_INVALID = 'operator_invalid';
    const TRANSACTION_FAIL = 'transaction_fail';
    const TRANSACTION_RESPONSE_CODE_SUCCESS = '00';
    const STATUS_ORDER_CANCELED = "order_canceled";


    /**
     * @var \SM\DigitalProduct\Api\TransactionRepositoryInterface
     */
    private $digitalProductTransaction;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @var \SM\Checkout\Model\SendOMS
     */
    protected $sendOMS;

    /**
     * PaymentSuccessUpdateBefore constructor.
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param \SM\DigitalProduct\Api\TransactionRepositoryInterface $digitalProductTransaction
     * @param \SM\Checkout\Model\SendOMS $sendOMS
     */
    public function __construct(
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \SM\DigitalProduct\Api\TransactionRepositoryInterface $digitalProductTransaction,
        \SM\Checkout\Model\SendOMS $sendOMS
    ) {
        $this->serializer = $serializer;
        $this->digitalProductTransaction = $digitalProductTransaction;
        $this->sendOMS = $sendOMS;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        // add inactive items to new quote after payment success
        $this->sendOMS->assignInactiveItemsToNewQuote($order);

        // handle order status with virtual order after payment success
        if ($order->getIsVirtual()) {
            $response = $this->handleDigitalProduct($order);
            if ($response['error'] == false) {
                $order->setState(Order::STATE_COMPLETE);
                $order->setStatus(Order::STATE_COMPLETE);
            } else {
                $order->setState(self::STATUS_ORDER_CANCELED);
                $order->setStatus(self::STATUS_ORDER_CANCELED);
                $order->setDigitalTransactionFail(1);
            }
        }

    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return array|bool
     */
    public function handleDigitalProduct(\Magento\Sales\Model\Order $order)
    {
        $data = $this->callDigitalProductApi($order);
        if ($data['error']) {
            return $data;
        } else {
            $apiResponse = $data['response'];
            if (!$apiResponse) {
                $data['error'] = true;
                $data['error_code'] = self::DIGITAL_API_FAIL;
                $data['message'] = __('Digital API Fail');
                return $data;
            }
            if (!$apiResponse->getStatus()
                || $apiResponse->getResponseCode() != self::TRANSACTION_RESPONSE_CODE_SUCCESS) {
                $data['error'] = true;
                $data['error_code'] = self::TRANSACTION_FAIL;
                $data['message'] = __('Transaction Fail');
            }
        }
        return $data;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function callDigitalProductApi(\Magento\Sales\Model\Order $order)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/digital-transaciont.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $customerId = $order->getCustomerId();
        $orderId = $order->getId();
        $result = ['error' => false, 'error_code' => '', 'error_message' => '', 'response' => ''];

        foreach ($order->getAllVisibleItems() as $item) {
            $product = $item->getProduct();
            $productId = $product->getData('product_id_vendor');
            $options = $item->getBuyRequest()->toArray();
            if (!$productId || $productId == '' || !isset($options['digital']) || !isset($options['service_type'])) {
                $result['error'] = true;
                $result['error_code'] = self::PRODUCT_ID_INVALID;
                $result['error_message'] = __('Product Id Invalid');
                break;
            }
            $serviceType = $options['service_type'];
            $digitalData = $options['digital'];
            if (isset($digitalData[DigitalInterface::MOBILE_NUMBER])
                && $digitalData[DigitalInterface::MOBILE_NUMBER] != '') {
                $customerNumber = $digitalData[DigitalInterface::MOBILE_NUMBER];
            } else {
                $result['error'] = true;
                $result['error_code'] = self::CUSTOMER_NUMBER_INVALID;
                $result['error_message'] = __('Customer number invalid');
                break;
            }

            try {
                switch ($serviceType) {
                    case \SM\DigitalProduct\Helper\Category\Data::MOBILE_PACKAGE_INTERNET_VALUE:
                    case \SM\DigitalProduct\Helper\Category\Data::TOP_UP_VALUE:
                        $response = $this->digitalProductTransaction->transactionMobile(
                            $customerId,
                            $customerNumber,
                            $productId,
                            $orderId
                        );
                        $result['response'] = $response;
                        break;
                    case \SM\DigitalProduct\Helper\Category\Data::MOBILE_POSTPAID_VALUE: // invalid, ignore
                        $response = $this->digitalProductTransaction->transactionMobilePostpaid(
                            $customerId,
                            $customerNumber,
                            $productId,
                            $orderId
                        );
                        $result['response'] = $response;
                        break;
                    case \SM\DigitalProduct\Helper\Category\Data::BPJS_KESEHATAN_VALUE: // invalid, ignore
                        $paymentPeriod = '';
                        $response = $this->digitalProductTransaction->transactionBpjs(
                            $customerId,
                            $customerNumber,
                            $paymentPeriod,
                            $productId,
                            $orderId
                        );
                        $result['response'] = $response;
                        break;
                    case \SM\DigitalProduct\Helper\Category\Data::ELECTRICITY_TOKEN_VALUE:
                        if (isset($digitalData[DigitalInterface::METER_NUMBER])
                            && $digitalData[DigitalInterface::METER_NUMBER] != '') {
                            $meterNumber = $digitalData[DigitalInterface::METER_NUMBER];
                        } else {
                            $result['error'] = true;
                            $result['error_code'] = self::METER_NUMBER_INVALID;
                            $result['error_message'] = __('Meter Number Invalid');
                            break;
                        }
                        $response = $this->digitalProductTransaction->transactionElectricityPrePaid(
                            $customerId,
                            $customerNumber,
                            $meterNumber,
                            $productId,
                            $orderId
                        );
                        $result['response'] = $response;
                        break;
                    case \SM\DigitalProduct\Helper\Category\Data::ELECTRICITY_BILL_VALUE:
                        $response = $this->digitalProductTransaction->transactionElectricityPostPaid(
                            $customerId,
                            $customerNumber,
                            $productId,
                            $orderId
                        );
                        $result['response'] = $response;
                        break;
                    case \SM\DigitalProduct\Helper\Category\Data::TELKOM_POSTPAID_VALUE: // invalid, ignore
                        $response = $this->digitalProductTransaction->transactionTelkom(
                            $customerId,
                            $customerNumber,
                            $productId,
                            $orderId
                        );
                        $result['response'] = $response;
                        break;
                    case \SM\DigitalProduct\Helper\Category\Data::PDAM_VALUE: // invalid, ignore
                        if (isset($digitalData[DigitalInterface::OPERATOR])
                            && $digitalData[DigitalInterface::OPERATOR] != '') {
                            $operatorCode = $digitalData[DigitalInterface::OPERATOR];
                        } else {
                            $result['error'] = true;
                            $result['error_code'] = self::OPERATOR_INVALID;
                            $result['error_message'] = __('Operator Invalid');
                            break;
                        }
                        $response = $this->digitalProductTransaction->transactionPdam(
                            $customerId,
                            $customerNumber,
                            $productId,
                            $operatorCode,
                            $orderId
                        );
                        $result['response'] = $response;
                        break;
                    default:
                        $result['error'] = true;
                        $result['error_code'] = self::NOT_DIGITAL_PRODUCT;
                        $result['error_message'] = __('Not Digital Product');
                }
                break;
            } catch (\Exception $e) {
                $result['error'] = true;
                $result['error_code'] = self::DIGITAL_API_FAIL;
                $result['error_message'] = __('Digital API Fail');
            }
        }
        $logger->info(print_r($result, true));
        return $result;
    }
}
