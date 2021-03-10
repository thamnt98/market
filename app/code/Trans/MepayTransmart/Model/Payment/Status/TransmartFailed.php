<?php
/**
 * @category Trans
 * @package  Trans_MepayTransmart
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2020 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\MepayTransmart\Model\Payment\Status;

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
use Trans\Mepay\Model\Payment\Status\Failed;
use Trans\Sprint\Helper\Config as SprintConfig;
use Trans\Mepay\Helper\Data;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Sales\Api\OrderManagementInterface;

class TransmartFailed extends Failed
{
  protected $eventManager;
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
    CartRepositoryInterface $quoteRepo,
    EventManager $eventManager,
    OrderManagementInterface $orderManagementInterface
  ) {
    $this->eventManager = $eventManager;
    parent::__construct($config, $transactionHelper, $customerHelper, $invoice, $logger, $quoteRepo, $orderManagementInterface);
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
        $transactionData = $this->transactionHelper->getTxnByTxnId($transaction->getId());
        foreach ($transactionData as $key => $value) {
          $orderId = $value->getOrderId();
          $order = $this->transactionHelper->getOrder($orderId);

          //update customer token
          if ($token) {
            $customerId = $order->getCustomerId();
            $this->customerHelper->setCustomerToken($customerId, $token);
          }

          //change status to void and close transaction
          $transactionObj = $this->transactionHelper->getTransaction($value->getTransactionId());
          $transactionObj->setTxnType(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_VOID);
          $transactionObj->close();
          $this->transactionHelper->saveTransaction($transactionObj);

          //save detail information
          $this->transactionHelper->addTransactionData($transactionObj->getTransactionId(), $inquiryTransaction, $transaction);

          //cancel order
          // $order->setState(Order::STATE_CANCELED);
          // $order->setStatus(Order::STATE_CANCELED);
          // $order->cancel();
          // $this->transactionHelper->saveOrder($order);
          $this->orderManagement->cancel($order->getId());
          $order->setStatus('order_canceled');
          $this->transactionHelper->saveOrder($order);
          $connection = Data::getConnection();
          $table = $connection->getTableName('sales_order_status_history');
          $query = "SELECT entity_id FROM ".$table." WHERE parent_id = '".$orderId."' and status = 'canceled'";
          $exist = $connection->fetchAll($query);
          if (count($exist)) {
              $this->logger->log('{{ Update sales order status history cancel exist start }}');
              $query = "UPDATE ".$table." set status = 'order_canceled' where status = 'canceled' ";
              $connection->query($query);
              $this->logger->log('{{ Update sales order status history cancel exist end }}');
          } else {
              $this->logger->log('{{ Update sales order status history cancel new start }}');
              $query = "INSERT INTO ".$table." (parent_id, status, entity_name) VALUES ('".$orderId."','order_canceled','order')";
              $connection->query($query);
              $this->logger->log('{{ Update sales order status history cancel new end }}');
          }

          /**
           * send order to oms
           */
          $this->sendOrderToOms($order);
        }

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

  /**
   * Send order to oms
   * @param   $order
   * @return void
   */
  public function sendOrderToOms($order)
  {
    $this->eventManager->dispatch(
      'update_payment_oms',
      [
        'reference_number' => $order->getReferenceNumber(),
        'payment_status' => SprintConfig::OMS_CANCEL_PAYMENT_ORDER,
      ]
    );
  }

}
