<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Sprint\Model\Order;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order;

/**
 * Class AutoCancel
 */
class AutoCancel implements \Trans\Sprint\Api\AutoCancelInterface
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Sales\CollectionFactory
     */
    protected $salesCollection;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order
     */
    protected $orderResource;

    /**
     * @var \Trans\IntegrationOrder\Api\PaymentStatusInterface
     */
    protected $paymentStatusOms;

    /**
     * @var \Trans\Sprint\Helper\Config
     */
    protected $configHelper;

    /**
     * @var \Trans\Sprint\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Trans\Sprint\Helper\Gateway
     */
    protected $gateway;

    /**
     * CancelOrderPending constructor.
     *
     * @param \Magento\Sales\Model\ResourceModel\Sales\CollectionFactory $salesCollection
     * @param \Magento\Sales\Model\ResourceModel\Order $orderResource
     * @param \Trans\IntegrationOrder\Api\PaymentStatusInterface $paymentStatusOms
     * @param \Trans\Sprint\Helper\Config $configHelper
     * @param \Trans\Sprint\Helper\Data $dataHelper
     * @param \Trans\Sprint\Helper\Gateway $gateway
     */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesCollection,
        \Magento\Sales\Model\ResourceModel\Order $orderResource,
        \Trans\IntegrationOrder\Api\PaymentStatusInterface $paymentStatusOms,
        \Trans\Sprint\Helper\Config $configHelper,
        \Trans\Sprint\Helper\Data $dataHelper,
        \Trans\Sprint\Helper\Gateway $gateway
    ) {
        $this->salesCollection = $salesCollection;
        $this->orderResource = $orderResource;
        $this->paymentStatusOms = $paymentStatusOms;
        $this->configHelper = $configHelper;
        $this->dataHelper = $dataHelper;
        $this->gateway = $gateway;

        $this->logger = $dataHelper->getLogger();
    }

    /**
     * {{@inheritDoc}}
     */
    public function cancelExpiredOrder(array $paymentCodes)
    {
        $this->logger->info(__FUNCTION__ . ' start ----------');

        $today = $this->dataHelper->getTodayTimezone("Y-m-d H:i:s");

        $this->logger->info('$today = ' . $today);

        try {
            if ($paymentCodes) {
                $string = '';
                foreach ($paymentCodes as $code) {
                    $string .= '"' . $code . '",';
                }
            }

            $status = $this->configHelper->getNewOrderStatus() ?: 'pending';

            $collection = $this->salesCollection->create();
            $collection->getSelect()
                ->join(
                    ['sprint' => 'sprint_response'],
                    'main_table.reference_number= sprint.transaction_no',
                    [
                        'payment_method' => 'sprint.payment_method', 
                        'expire' => 'sprint.expire_date', 
                        'insert_date' => 'sprint.insert_date', 
                        'channel_id' => 'sprint.channel_id'
                    ]
                );
            $collection->setPageSize(50);
            $collection->addFieldToFilter('payment_method', ['in' => $paymentCodes]);
            $collection->addFieldToFilter('status', $status);
            $collection->addFieldToFilter('expire_date', ['lteq' => $today]);

            if ($collection->getSize()) {
                
                $orderIds = [];
                /** @var Order $order */
                foreach ($collection as $order) {
                    if ($order instanceof \Magento\Sales\Api\Data\OrderInterface) {
                        $this->logger->info('===> start loop cancel order.');
                        try {
                            $checkStatus = $this->gateway->checkTrxStatus($order);

                            if(!$checkStatus) {
                                /**
                                 * Digital Order is not sent to OMS
                                 * Reference: APO-1418
                                 */
                                if ($order->getIsVirtual()) {
                                    $this->logger->info('Virtual Order -> Skip');
                                    continue;
                                }

                                $this->logger->info('Order BCA VA expired.');
                                $this->logger->info('$refNumber = ' . $order->getOrder('reference_number'));
                                $this->logger->info('$orderEntityId = ' . $order->getEntityId());
                                $orderIds[] = (int) $order->getEntityId();
                                
                                $refNumber     = $order->getData('reference_number');
                                $updateOms = $this->paymentStatusOms->sendStatusPayment($refNumber, 99);
                                if ($updateOms) {
                                    $updateOms = json_encode($updateOms);
                                }
                                $this->logger->info('$updateOms response =' . $updateOms);
                            }
                        } catch (InputException $e) {
                            $this->logger->info($e->getMessage());
                            $this->logger->info('===> end. next loop cancel order.');
                            continue;
                        } catch (NoSuchEntityException $e) {
                            $this->logger->info($e->getMessage());
                            $this->logger->info('===> end. next loop cancel order.');
                            continue;
                        } catch (\Exception $e) {
                            $this->logger->info($e->getMessage());
                            continue;
                        }
                    }
                    $this->logger->info('===> end. next loop cancel order.');
                }

                $this->cancelOrders($orderIds);
            }
        } catch (\Exception $e) {
            $this->logger->info('Error = ' . $e->getMessage());
        }

        $this->logger->info(__FUNCTION__ . ' end ----------');
    }

    /**
     * Cancel order by reference number
     * @param  array $orderId
     * @return void
     */
    protected function cancelOrders(array $orderIds)
    {
        $connection = $this->orderResource->getConnection();
        $tableSales = $connection->getTableName('sales_order');
        $orderStatus = 'order_canceled';
        $orderState = 'canceled';
        
        $connection->update($tableSales, ['status' => $orderStatus, 'state' => $orderState], ['entity_id IN (?)' => $orderIds]);
        
        $historyData = [];
        foreach ($orderIds as $orderId) {
            $history['parent_id'] = $orderId;
            $history['status'] = $orderStatus;
            // $history['comment'] = 'Order Canceled by Transmart';
            $history['entity_name'] = 'order';
            $historyData[] = $history;
        }


        $historyTable = $connection->getTableName('sales_order_status_history');
        $connection->insertOnDuplicate($historyTable, $historyData);
    }
}
