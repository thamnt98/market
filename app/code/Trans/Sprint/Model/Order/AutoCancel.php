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
     * @var \Trans\Sprint\Api\PaymentNotifyInterface
     */
    protected $notifyInterface;

    /**
     * @var \Trans\Sprint\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Trans\Sprint\Helper\Gateway
     */
    protected $gateway;

    /**
     * Application Event Dispatcher
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * CancelOrderPending constructor.
     *
     * @param \Magento\Sales\Model\ResourceModel\Sales\CollectionFactory $salesCollection
     * @param \Magento\Sales\Model\ResourceModel\Order $orderResource
     * @param \Trans\IntegrationOrder\Api\PaymentStatusInterface $paymentStatusOms
     * @param \Trans\Sprint\Api\PaymentNotifyInterface $notifyInterface
     * @param \Trans\Sprint\Helper\Config $configHelper
     * @param \Trans\Sprint\Helper\Data $dataHelper
     * @param \Trans\Sprint\Helper\Gateway $gateway
     */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesCollection,
        \Magento\Sales\Model\ResourceModel\Order $orderResource,
        \Trans\IntegrationOrder\Api\PaymentStatusInterface $paymentStatusOms,
        \Trans\Sprint\Api\PaymentNotifyInterface $notifyInterface,
        \Trans\Sprint\Helper\Config $configHelper,
        \Trans\Sprint\Helper\Data $dataHelper,
        \Trans\Sprint\Helper\Gateway $gateway,
        \Magento\Framework\Event\ManagerInterface $_eventManager
    ) {
        $this->salesCollection = $salesCollection;
        $this->orderResource = $orderResource;
        $this->paymentStatusOms = $paymentStatusOms;
        $this->notifyInterface = $notifyInterface;
        $this->configHelper = $configHelper;
        $this->dataHelper = $dataHelper;
        $this->gateway = $gateway;
        $this->_eventManager = $_eventManager;

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
            $collection->getSelect()->group('reference_number');

            if ($collection->getSize()) {

                // $orderIds = [];
                $refNumbers = [];
                /** @var Order $order */
                foreach ($collection as $order) {
                    if ($order instanceof \Magento\Sales\Api\Data\OrderInterface) {

                        /**
                         * Digital Order is not sent to OMS
                         * Reference: APO-1418
                         */
                        if ($order->getIsVirtual()) {
                            $this->logger->info('Virtual Order -> Skip');
                            continue;
                        }

                        $this->logger->info('===> start loop cancel order.');

                        /**
                         * Handle Send email payment expire for VA payment.
                         * Reference: APO-5552
                         */
                        $this->_eventManager->dispatch('trans_sprint_set_order_cancel_after', ['order' => $order]);


                        try {
                            $checkStatus = $this->gateway->checkTrxStatus($order);
                            $check = $checkStatus['status'];

                            $refNumber = $order->getData('reference_number');

                            if(!$check) {
                                $this->logger->info('Order BCA VA expired.');
                                $this->logger->info('$refNumber = ' . $refNumber);
                                $this->logger->info('$orderEntityId = ' . $order->getEntityId());
                                // $orderIds[] = (int) $order->getEntityId();

                                if(!in_array($refNumber, $refNumbers) && $order->getStatus() != 'order_canceled') {
                                    $updateOms = $this->paymentStatusOms->sendStatusPayment($refNumber, 99);
                                    if ($updateOms) {
                                        $updateOms = json_encode($updateOms);
                                    }
                                    $this->logger->info('$updateOms response =' . $updateOms);
                                }
                            }

                            if(!in_array($refNumber, $refNumbers)) {
                                if(!$check) {
                                    $refNumbers[] = $refNumber;
                                }

                                $postData = isset($checkStatus['response']['queryResponse'][0]['transactionNo']) ? $checkStatus['response']['queryResponse'][0] : null;

                                if(empty($postData)) {
                                    continue;
                                }

                                $this->notifyInterface->processingNotify($postData);
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

                if($refNumbers) {
                    $this->cancelOrders($refNumbers);
                    $this->saveStatusHistoryOrder($refNumbers);
                }
            }
        } catch (\Exception $e) {
            $this->logger->info('Error = ' . $e->getMessage());
        }

        $this->logger->info(__FUNCTION__ . ' end ----------');
    }

    /**
     * Cancel order by reference number
     * @param  array $refNumbers
     * @return void
     */
    protected function cancelOrders(array $refNumbers)
    {
        $connection = $this->orderResource->getConnection();
        $tableSales = $connection->getTableName('sales_order');
        $orderStatus = 'order_canceled';
        $orderState = 'canceled';

        $connection->update($tableSales, ['status' => $orderStatus, 'state' => $orderState], ['reference_number IN (?)' => $refNumbers]);
    }

    /**
     * Order status history 'cancel order'
     * @param  array $refNumbers
     * @return void
     */
    protected function saveStatusHistoryOrder(array $refNumbers)
    {
        $connection = $this->orderResource->getConnection();
        $orderStatus = 'order_canceled';

        $orders = $this->salesCollection->create();
        $orders->addFieldToFilter('reference_number', ['in' => $refNumbers]);

        if ($orders->getSize()) {
            $historyData = [];
            foreach ($orders as $order) {
                $history['parent_id'] = $order->getId();
                $history['status'] = $orderStatus;
                // $history['comment'] = 'Order Canceled by Transmart';
                $history['entity_name'] = 'order';
                $historyData[] = $history;
            }

            $this->logger->info('Param History = ' . print_r($historyData, true));

            $historyTable = $connection->getTableName('sales_order_status_history');
            $connection->insertOnDuplicate($historyTable, $historyData);
        }
    }
}
