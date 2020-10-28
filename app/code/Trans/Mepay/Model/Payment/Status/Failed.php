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
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Sales\Model\Order;

class Failed
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
   * @var 
   */
  protected $quoteRepo;

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
    LoggerWrite $logger,
    CartRepositoryInterface $quoteRepo
  ) {
    $this->config = $config;
    $this->transactionHelper = $transactionHelper;
    $this->customerHelper = $customerHelper;
    $this->invoice = $invoice;
    $this->logger = $logger;
    $this->quoteRepo = $quoteRepo;
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

        //update customer token
        if ($token) {
          $customerId = $order->getCustomerId();
          $this->customerHelper->setCustomerToken($customerId, $token);
        }

        //change status to void and close transaction
        $transactionObj = $this->transactionHelper->getTransaction($transactionData->getTransactionId());
        $transactionObj->setTxnType(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_VOID);
        //$transactionObj->close();
        $this->transactionHelper->saveTransaction($transactionObj);

        //save detail information
        $this->transactionHelper->addTransactionData($transaction->getId(), $inquiryTransaction, $transaction);

        //cancel order
        $order->setState(Order::STATE_CANCELED);
        $order->setStatus(Order::STATE_CANCELED);
        $order->cancel();
        $this->transactionHelper->saveOrder($order);
        //restore cart
        //$quote = $this->quoteRepo->get($order->getQuoteId());
        //$quote->setIsActive(1)->setReservedOrderId(null);
        //$this->quoteRepo->save($quote);

    } catch (InputException $e) {
      throw $e;
    } catch (NoSuchEntityException $e) {
      throw $e;
    } catch (\Exception $e) {
      throw $e;
    }
  }

}
