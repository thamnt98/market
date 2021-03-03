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
namespace Trans\Mepay\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\ResourceModel\Order as OrderResource;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Event\ManagerInterface;
use Trans\Sprint\Helper\Config as SprintConfig;
use Trans\Mepay\Logger\LoggerWrite;

class Order extends AbstractHelper
{
    /**
     * @var int
     */
    const LIMIT_ORDER = 50;

    /**
     * @var array
     */
    const PAYMENT_EXPIRATION_ON_SECOND = [
        \Trans\Mepay\Model\Config\Provider\Cc::CODE_CC => 0,
        \Trans\Mepay\Model\Config\Provider\Qris::CODE_QRIS => 0,
        \Trans\Mepay\Model\Config\Provider\Va::CODE_VA => 0,
        \Trans\Mepay\Model\Config\Provider\Debit::CODE => 0,
        \Trans\Mepay\Model\Config\Provider\CcDebit::CODE => 0,
        'others'=> 0
    ];

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order
     */
    protected $orderResource;

    /**
     * @var \Magento\Sales\Api\OrderManagementInterface
     */
    protected $orderManagement;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface;
     */
    protected $orderRepo;

    /**
     * @var use Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Trans\Mepay\Logger\LoggerWrite
     */
    protected $logger;

    /**
     * Constructor
     * @param Context $context
     * @param OrderResource $orderResource
     * @param OrderManagementInterface $orderManagement
     * @param OrderRepositoryInterface $orderRepo
     * @param ManagerInterface $eventManager
     * @param LoggerWrite $logger
     */
    public function __construct(
        Context $context,
        OrderResource $orderResource,
        OrderManagementInterface $orderManagement,
        OrderRepositoryInterface $orderRepo,
        ManagerInterface $eventManager,
        LoggerWrite $logger
    ) {
        $this->_eventManager = $eventManager;
        $this->orderResource = $orderResource;
        $this->orderManagement = $orderManagement;
        $this->orderRepo = $orderRepo;
        $this->eventManager = $eventManager;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Get pending order
     * @param  int|integer $maxHours
     * @param  string $status
     * @return array
     */
    public function getPendingOrder(int $maxHours = 0, string $status)
    {
        $connection = $this->orderResource->getConnection();
        $table = $connection->getTableName('sales_order');
        $query = $connection->select();
        $result =  $query
            ->from($table, ['*'])
            ->where('status = "'.$status.'" and created_at < DATE_SUB(NOW(),INTERVAL '.$maxHours.' HOUR)')
            ->limit(self::LIMIT_ORDER)
        ;
        $this->logger->log($result->__toString());
        return $result->query()->fetchAll();
    }

    /**
     * Get last order by customerId
     * @param  int $customerId
     * @return array
     */
    public function getLastOrderByCustomerId(int $customerId)
    {
        $connection = $this->orderResource->getConnection();
        $table = $connection->getTableName('sales_order');
        $query = $connection->select();
        $result = $query
            ->from($table, ['*'])
            ->where('customer_id = "'.$customerId.'"')
            ->order('entity_id DESC')
            ->limit(1)
        ;
        return $result->query()->fetch();
    }

    /**
     * Is payment expired
     * @param  int $orderId
     * @param  string $createdAt
     * @return boolean
     */
    public function isOrderPaymentIsExpired(int $orderId, string $createdAt)
    {
        $instance = null;
        $payment = $this->getPaymentData($orderId);
        if ($payment['method'] == \Trans\Mepay\Model\Config\Provider\Cc::CODE_CC)
            $instance = Data::getClassInstance('Trans\Mepay\Model\Config\Provider\Cc\Expire');
        if ($payment['method'] == \Trans\Mepay\Model\Config\Provider\CcDebit::CODE)
            $instance = Data::getClassInstance('Trans\Mepay\Model\Config\Provider\CcDebit\Expire');
        if ($payment['method'] == \Trans\Mepay\Model\Config\Provider\Debit::CODE)
            $instance = Data::getClassInstance('Trans\Mepay\Model\Config\Provider\Debit\Expire');
        if ($payment['method'] == \Trans\Mepay\Model\Config\Provider\Qris::CODE_QRIS)
            $instance = Data::getClassInstance('Trans\Mepay\Model\Config\Provider\Qris\Expire');
        if ($payment['method'] == \Trans\Mepay\Model\Config\Provider\Va::CODE_VA)
            $instance = Data::getClassInstance('Trans\Mepay\Model\Config\Provider\Va\Expire');
        if ($payment['method'] == \Trans\Mepay\Model\Config\Provider\AllbankCc::CODE)
            $instance = Data::getClassInstance('Trans\Mepay\Model\Config\Provider\AllbankCc\Expire');
        if ($payment['method'] == \Trans\Mepay\Model\Config\Provider\AllbankDebit::CODE)
            $instance = Data::getClassInstance('Trans\Mepay\Model\Config\Provider\AllbankDebit\Expire');
        if ($instance)
            return $instance->isExpired($orderId);
        throw new \Exception("Error Processing Request", 1);
    }

    /**
     * Get payment expiration
     * @param  string $code
     * @return int
     */
    public function getPaymentExpiration(string $code)
    {
        if(array_key_exists($code, self::PAYMENT_EXPIRATION_ON_SECOND)) {
            return self::PAYMENT_EXPIRATION_ON_SECOND[$code];
        }
        return self::PAYMENT_EXPIRATION_ON_SECOND['others'];
    }

    /**
     * Get payment data
     * @param  int $orderId
     * @return array
     */
    public function getPaymentData(int $orderId)
    {
        $connection = $this->orderResource->getConnection();
        $table = $connection->getTableName('sales_order_payment');
        $query = $connection->select();
        $result = $query
            ->from($table, ['*'])
            ->where('parent_id = '.$orderId)
            ->limit(1)
        ;
        return $result->query()->fetch();
    }

    public function getQuoteIdByReffNumber(string $refNumber)
    {
        $connection = $this->orderResource->getConnection();
        $tableSales = $connection->getTableName('sales_order');
        $salesData = $connection->select();
        $salesData->from($tableSales, ['*'])->where('reference_number = ?', $refNumber);
        $salesData = $connection->fetchRow($salesData);
        return $salesData['quote_id'];
    }

    /**
     * Cancel order
     * @param  int $orderId
     * @param  bool $updateOms
     * @return void
     */
    public function doCancelationOrder (int $orderId, $updateOms = true)
    {
        $this->orderManagement->cancel($orderId);
        $order = $this->orderRepo->get($orderId);
        $order->setStatus('order_canceled');
        $this->orderRepo->save($order);

        $connection = $this->orderResource->getConnection();
        $table = $connection->getTableName('sales_order_status_history');
        $query = "SELECT entity_id FROM ".$table." WHERE parent_id = '".$orderId."' and status = 'canceled'";
        $exist = $connection->fetchAll($query);
        if (count($exist)) {
            $query = "UPDATE ".$table." set status = 'order_canceled' where status = 'canceled' ";
            $connection->query($query);
        } else {
            $query = "INSERT INTO ".$table." (parent_id, status, entity_name) VALUES ('".$orderId."','order_canceled','order')";
            $connection->query($query);
        }

        if($updateOms) {
            $this->eventManager->dispatch(
                'update_payment_oms',
                [
                    'reference_number' => $order->getReferenceNumber(),
                    'payment_status' => SprintConfig::OMS_CANCEL_PAYMENT_ORDER,
                ]
            );
        }
    }

    /**
     * Cancel order by reference number
     * @param  int $orderId
     * @param  bool $updateOms
     * @return void
     */
    public function doCancelationOrderByRefNumber (string $refNumber)
    {
        $connection = $this->orderResource->getConnection();
        $tableSales = $connection->getTableName('sales_order');
        $orderStatus = 'order_canceled';
        $orderState = 'canceled';
        
        $salesData = $connection->select();
        $salesData->from(
                $tableSales,
                ['*']
            )->where('reference_number = ?', $refNumber);

        $salesData = $connection->fetchAll($salesData);

        $orderIds = [];
        $historyData = [];
        foreach ($salesData as $order) {
            $orderIds[] = $order['entity_id'];
            
            $history['parent_id'] = $order['entity_id'];
            $history['status'] = $orderStatus;
            $history['comment'] = 'Order Canceled by Transmart';
            $history['entity_name'] = 'order';
            $historyData[] = $history;
        }

        $connection->update($tableSales, ['status' => $orderStatus, 'state' => $orderState], ['entity_id IN (?)' => $orderIds]);

        $historyTable = $connection->getTableName('sales_order_status_history');
        $connection->insertOnDuplicate($historyTable, $historyData);        
    }
}

