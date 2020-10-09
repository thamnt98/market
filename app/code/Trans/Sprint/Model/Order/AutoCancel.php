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

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderStatusHistoryInterfaceFactory;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\OrderStatusHistoryRepositoryInterface;
use Magento\Sales\Model\Order;

/**
 * Class AutoCancel
 */
class AutoCancel implements \Trans\Sprint\Api\AutoCancelInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var FilterGroup
     */
    private $filterGroup;

    /**
     * @var OrderManagementInterface
     */
    private $orderManagement;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Sales\CollectionFactory
     */
    protected $salesCollection;

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
     * CancelOrderPending constructor.
     *
     * @param OrderRepositoryInterface $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param FilterGroup $filterGroup
     * @param OrderManagementInterface $orderManagement
     * @param \Magento\Sales\Model\ResourceModel\Sales\CollectionFactory $salesCollection
     * @param \Trans\IntegrationOrder\Api\PaymentStatusInterface $paymentStatusOms
     * @param \Trans\Sprint\Helper\Config $configHelper
     * @param \Trans\Sprint\Helper\Data $dataHelper
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        OrderStatusHistoryInterfaceFactory $orderStatusHistoryInterfaceFactory,
        OrderStatusHistoryRepositoryInterface $orderStatusHistoryRepoInterface,
        FilterBuilder $filterBuilder,
        FilterGroup $filterGroup,
        OrderManagementInterface $orderManagement,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesCollection,
        \Trans\IntegrationOrder\Api\PaymentStatusInterface $paymentStatusOms,
        \Trans\Sprint\Helper\Config $configHelper,
        \Trans\Sprint\Helper\Data $dataHelper
    ) {
        $this->orderRepository                    = $orderRepository;
        $this->searchCriteriaBuilder              = $searchCriteriaBuilder;
        $this->filterBuilder                      = $filterBuilder;
        $this->orderStatusHistoryInterfaceFactory = $orderStatusHistoryInterfaceFactory;
        $this->orderStatusHistoryRepoInterface    = $orderStatusHistoryRepoInterface;
        $this->filterGroup                        = $filterGroup;
        $this->orderManagement                    = $orderManagement;
        $this->salesCollection                    = $salesCollection;
        $this->paymentStatusOms                   = $paymentStatusOms;
        $this->configHelper                       = $configHelper;
        $this->dataHelper                         = $dataHelper;

        $writer       = new \Zend\Log\Writer\Stream(BP . '/var/log/auto_cancel.log');
        $logger       = new \Zend\Log\Logger();
        $this->logger = $logger->addWriter($writer);
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
                    array('sprint' => 'sprint_response'),
                    'main_table.reference_number= sprint.transaction_no',
                    array('payment_method' => 'sprint.payment_method', 'expire' => 'sprint.expire_date')
                );
            // $collection->getSelect()->where('payment_method in (' . $string . ')');
            $collection->setPageSize(50);
            $collection->addFieldToFilter('payment_method', ['in' => $paymentCodes]);
            $collection->addFieldToFilter('status', $status);
            $collection->addFieldToFilter('expire_date', ['lteq' => $today]);

            if ($collection->getSize()) {
                /** @var Order $order */
                foreach ($collection as $order) {
                    if ($order instanceof \Magento\Sales\Api\Data\OrderInterface) {
                        try {
                            $this->orderManagement->cancel($order->getId()); //cancel order
                            // $orderEntityId = $order->getEntityId();
                            // $orderHistory  = $this->orderStatusHistoryInterfaceFactory->create();
                            // $orderHistory->setParentId($orderEntityId);
                            // $orderHistory->setStatus('canceled');
                            // $this->orderStatusHistoryRepoInterface->save($orderHistory);
                            $refNumber = $order->getData('reference_number');

                            $this->logger->info('$refNumber = ' . $refNumber);

                            /**
                             * Digital Order is not sent to OMS
                             * Reference: APO-1418
                             */
                            if ($order->getIsVirtual()) {
                                $this->logger->info('Virtual Order -> Skip');
                                continue;
                            }

                            $updateOms = $this->paymentStatusOms->sendStatusPayment($refNumber, 99);
                            if ($updateOms) {
                                $updateOms = json_encode($updateOms);
                            }
                            $this->logger->info('$updateOms response =' . $updateOms);
                        } catch (InputException $e) {
                            $this->logger->info($e->getMessage());
                            continue;
                        } catch (NoSuchEntityException $e) {
                            $this->logger->info($e->getMessage());
                            continue;
                        } catch (\Exception $e) {
                            $this->logger->info($e->getMessage());
                            continue;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $this->logger->info('Error = ' . $e->getMessage());
        }

        $this->logger->info(__FUNCTION__ . ' end ----------');
    }
}
