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
use Trans\Mepay\Model\Payment\Status\Capture;
use Trans\Sprint\Helper\Config as SprintConfig;
use Trans\Mepay\Helper\Data;
use Magento\Framework\Event\ManagerInterface as EventManager;

class TransmartCapture extends Capture
{
  protected $eventManager;
  /**
   * Constructor
   * @param Config            $config            [description]
   * @param TransactionHelper $transactionHelper [description]
   * @param CustomerHelper    $customerHelper    [description]
   * @param Invoice           $invoice           [description]
   * @param LoggerWrite       $logger            [description]
   */
  public function __construct(
      Config $config,
      TransactionHelper $transactionHelper,
      CustomerHelper $customerHelper,
      Invoice $invoice,
      LoggerWrite $logger,
      EventManager $eventManager
    ) {
      $this->eventManager = $eventManager;
      parent::__construct($config, $transactionHelper, $customerHelper,$invoice,$logger);
    }

  /**
   * Handle description
   * @param  \Trans\Mepay\Api\Data\TransactionInterface $transaction
   * @param  \Magento\Sales\Model\ResourceModel\Order\Payment\Transaction\Collection $inquiryTransaction
   * @return void
   */
  public function handle($transaction, $inquiryTransaction, $token = null, $isPreAuth = false)
  {
    try {
        //init related data
        $transactionData = $this->transactionHelper->getTxnByTxnId($transaction->getId());
        if (!$transactionData->getSize() && $isPreAuth) {
            $transactionData = $this->transactionHelper->getTxnByTxnId($inquiryTransaction->getId());
        }
        foreach ($transactionData as $key => $value) {
            $orderId = $value->getOrderId();
            $this->logger->log('== {{order_id:'.$orderId.'}} ==');
            $order = $this->transactionHelper->getOrder($orderId);
            $payment = $order->getPayment();
            $this->logger->log('== {{payment_id:'.$payment->getId().'}} ==');
            //update customer token
            if ($token) {
              $customerId = $order->getCustomerId();
              $this->customerHelper->setCustomerToken($customerId, $token);
            }

            //close authorize transaction
            $this->logger->log('== {{txnid:'.$value->getTransactionId().':close}} ==');
            $transactionObj = $this->transactionHelper->getTransaction($value->getTransactionId());
            $transactionObj->close();

            //create capture transaction
            $transactionCapture = $this->transactionHelper->buildCaptureTransaction($payment, $order, $transaction);

            //update payment
            $this->logger->log('== {{txnid:'.$transactionCapture->getTransactionId().':capture}} ==');
            $payment->setLastTransactionId($transaction->getId());
            //$payment->addTransactionCommentsToOrder($transactionCapture, $message);
            //$payment->setAdditionalInformation([Transaction::RAW_DETAILS => $transaction->getData()]);

            //create invoice
            $this->createOrderInvoiceParentAndChild($order, $transaction);
            //save detail information
            $this->transactionHelper->addTransactionData($transactionCapture->getTransactionId(), $inquiryTransaction, $transaction);

            /**
             * Send order to oms
             */
            $this->logger->log('{{oms-event-dispatch:start: '.$order->getIncrementId().'}}');
            $this->sendOrderToOms($order);
            $this->logger->log('{{oms-event-dispatch:end: '.$order->getIncrementId().'}}');

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
    if ($order->getIsParent()) {
        $this->eventManager->dispatch(
            'update_payment_oms',
            [
              'reference_number' => $order->getReferenceNumber(),
              'payment_status' => SprintConfig::OMS_SUCCESS_PAYMENT_OPRDER,
            ]
        );
    }
  }

  /**
   * Update child sales order status history
   * @param $connection
   * @param $orderId
   * @return void
   */
  public function updateChildStatusHistory($orderId)
  {
    $connection = Data::getConnection();
    $table = $connection->getTableName('sales_order_status_history');
    $query = "SELECT entity_id FROM ".$table." WHERE parent_id = '".$orderId."' and status = 'in_process'";
    $exist = $connection->fetchAll($query);
    if (!count($exist)) {
        $this->logger->log('{{ Update sales order status history start }}');
        $query = "INSERT INTO ".$table." (parent_id, status, entity_name) VALUES ('".$orderId."','in_process','order')";
        $connection->query($query);
        $this->logger->log('{{ Update sales order status history end }}');
    }
  }

  public function createOrderInvoiceParentAndChild($initOrder, $transaction)
  {
        if ($initOrder->getIsParent()) {
            $invoice = $this->invoice->create($initOrder, $transaction);
            $this->invoice->send($invoice);
        }
        $initOrder->setState('in_process');
        $initOrder->setStatus('in_process');
        $this->transactionHelper->saveOrder($initOrder);
        $this->updateChildStatusHistory($initOrder->getId());
  }
}
