<?php
/**
 * @category Trans
 * @package  Trans_Mepay
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2020 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\Mepay\Model\Payment\Status;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order\Payment\Transaction;
use Trans\Mepay\Model\Config\Config;
use Trans\Mepay\Helper\Payment\Transaction as TransactionHelper;
use Trans\Mepay\Helper\Customer\Customer as CustomerHelper;
use Trans\Mepay\Model\Invoice;
use Trans\Mepay\Logger\LoggerWrite;

class Paid
{
  /**
   * @var Config
   */
  protected $config;

  /**
   * @var Transaction
   */
  protected $transactionHelper;

  /**
   * @var Customer
   */
  protected $customerHelper;

  /**
   * @var Invoice
   */
  protected $invoice;

  /**
   * @var Logger
   */
  protected $logger;

  /**
   * Constructor
   * @param Config            $config
   * @param TransactionHelper $transactionHelper
   * @param Invoice           $invoice
   * @param LoggerWrite       $logger
   */
  public function __construct(
    Config $config,
    TransactionHelper $transactionHelper,
    CustomerHelper $customerHelper,
    Invoice $invoice,
    LoggerWrite $logger
  ) {
    $this->config = $config;
    $this->transactionHelper = $transactionHelper;
    $this->customerHelper = $customerHelper;
    $this->invoice = $invoice;
    $this->logger = $logger;
  }

  /**
   * Handle description
   * @param  \Trans\Mepay\Api\Data\TransactionInterface $transaction
   * @param  \Magento\Sales\Model\ResourceModel\Order\Payment\Transaction\Collection $inquiryTransaction
   * @return void
   */
  public function handle($transaction, $inquiryTransaction, $token = null)
  {
    try {

        //init related data
        $transactionData = $this->transactionHelper->getAuthorizeByTxnId($transaction->getId())->getFirstItem();
        $orderId = $transactionData->getOrderId();
        $order = $this->transactionHelper->getOrder($orderId);
        $payment = $order->getPayment();

        //update customer token
        if ($token) {
          $customerId = $order->getCustomerId();
          $this->customerHelper->setCustomerToken($customerId, $token);
        }

        //close authorize transaction
        $transactionObj = $this->transactionHelper->getTransaction($transactionData->getTransactionId());
        $transactionObj->close();

        //create capture transaction
        $transactionCapture = $this->transactionHelper->buildCaptureTransaction($payment, $order, $transaction);

        //update payment
        $payment->setLastTransactionId($transaction->getId());
        //$payment->addTransactionCommentsToOrder($transactionCapture, $message);
        $payment->setAdditionalInformation([Transaction::RAW_DETAILS => $transaction->getData()]);

        //create invoice
        $invoice = $this->invoice->create($order, $transaction);
        $this->invoice->send($invoice);

        //save detail information
        $this->transactionHelper->addTransactionData($transaction->getId(), $inquiryTransaction, $transaction);

    } catch (InputException $e) {
      $this->logger->log($e->getMessage());
      throw $e;
    } catch (NoSuchEntityException $e) {
      $this->logger->log($e->getMessage());
      throw $e;
    } catch (\Exception $e) {
      $this->logger->log($e->getMessage());
      throw $e;
    }
  }
}
