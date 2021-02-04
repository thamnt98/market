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
namespace Trans\Mepay\Cron\Transaction;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Trans\Mepay\Helper\Order as CustomOrder;
use Trans\Mepay\Helper\Search as SearchBuilder;
use Trans\Mepay\Helper\Data;
use Trans\Sprint\Helper\Config as SprintConfig;
use Trans\Mepay\Logger\LoggerWrite;

class AutoCancel 
{
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepo;

    /**
     * @var \Magento\Sales\Api\OrderManagementInterface
     */
    protected $orderManagement;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var Trans\Mepay\Helper\Order
     */
    protected $customOrder;

    /**
     * @var use Trans\Mepay\Helper\Search
     */
    protected $searchBuilder;

    protected $logger;

    /**
     * @var int
     */
    const MAXIMUM_PENDING_HOUR = 4;

    /**
     * @var string
     */
    const STATUS_FILTER = 'pending_payment';

    /**
     * Constructor method
     * @param CustomOrder $customOrder
     */
    public function __construct(
        OrderRepositoryInterface $orderRepo,
        OrderManagementInterface $orderManagement,
        EventManager $eventManager,
        CustomOrder $customOrder,
        SearchBuilder $searchBuilder,
        LoggerWrite $logger
    ) {
        $this->orderRepo = $orderRepo;
        $this->orderManagement = $orderManagement;
        $this->eventManager = $eventManager;
        $this->customOrder = $customOrder;
        $this->searchBuilder = $searchBuilder;
        $this->logger = $logger;
    }

    /**
     * Cron gateway execution
     * @return void
     */
    public function execute()
    {
        $this->logger->log('===auto-cancel-logger-start===');
        $orders = $this->customOrder->getPendingOrder(self::MAXIMUM_PENDING_HOUR, self::STATUS_FILTER);
        $this->logger->log(print_r($orders, true));
        if (count($orders)) {
            foreach ($orders as $key => $value) {
                $this->logger->log("\n");
                $this->logger->log('=====before');
                $this->logger->log('order_id: '.$value['entity_id']);
                $this->logger->log('status: '.$value['status']);
                $this->logger->log('state: '.$value['state']);

                $this->orderManagement->cancel($value['entity_id']);
                $order = $this->orderRepo->get($value['entity_id']);
                $order->setStatus('order_canceled');
                $this->orderRepo->save($order);

                $this->logger->log('=====after');
                $this->logger->log('order_id: '.$order->getId());
                $this->logger->log('status: '.$order->getStatus());
                $this->logger->log('state: '.$order->getState());

                $connection = Data::getConnection();
                $table = $connection->getTableName('sales_order_status_history');
                $query = "SELECT entity_id FROM ".$table." WHERE parent_id = '".$value['entity_id']."' and status = 'canceled'";
                $exist = $connection->fetchAll($query);
                if (count($exist)) {
                    $query = "UPDATE ".$table." set status = 'order_canceled' where status = 'canceled' ";
                    $connection->query($query);
                } else {
                    $query = "INSERT INTO ".$table." (parent_id, status, entity_name) VALUES ('".$value['entity_id']."','order_canceled','order')";
                    $connection->query($query);
                }

                $this->logger->log('=====update_status_history');
                $this->logger->log($query);

                $this->logger->log('=====dispatch_oms_start');
                $this->eventManager->dispatch(
                    'update_payment_oms',
                    [
                        'reference_number' => $order->getReferenceNumber(),
                        'payment_status' => SprintConfig::OMS_CANCEL_PAYMENT_ORDER,
                    ]
                );
                $this->logger->log('=====dispatch_oms_end');
            }
        }
        $this->logger->log('===auto-cancel-logger-end===');
    }
}
