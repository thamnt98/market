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

namespace Trans\Sprint\Helper;

use \Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class SalesOrder
 */
class SalesOrder extends \Magento\Framework\App\Helper\AbstractHelper
{
	/**
	 * @var \Magento\Sales\Api\OrderRepositoryInterface
	 */
	protected $salesOrder;

	/**
	 * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
	 */
	protected $orderCollection;

	/**
	 * @var \Magento\Framework\Api\FilterBuilder
	 */
	protected $filterBuilder;

	/**
	 * @var \Magento\Framework\Api\SearchCriteriaBuilder
	 */
	protected $searchBuilder;

	/**
	 * @param \Magento\Framework\App\Helper\Context $context
	 * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollection
	 * @param \Magento\Sales\Api\OrderRepositoryInterface $salesOrder
	 * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
	 * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchBuilder
	 */
	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollection,
		\Magento\Sales\Api\OrderRepositoryInterface $salesOrder,
		\Magento\Framework\Api\FilterBuilder $filterBuilder,
		\Magento\Framework\Api\SearchCriteriaBuilder $searchBuilder
	) {
		$this->salesOrder = $salesOrder;
		$this->orderCollection = $orderCollection;
		$this->filterBuilder = $filterBuilder;
		$this->searchBuilder = $searchBuilder;

		parent::__construct($context);
	}

	/**
	 * Get sub-orders by reference_payment_number and increment id
	 *
	 * @param string $refNumber
	 * @return \Magento\Sales\Model\ResourceModel\Order\Collection
	 */
	public function getSubOrdersWithInc(string $refNumber)
	{
		$result = null;
		$this->searchBuilder->addFilter('reference_number', $refNumber);
		$this->searchBuilder->addFilter('is_parent', 1, 'neq');

		$searchCriteria = $this->searchBuilder->create();
		$searchResults = $this->salesOrder->getList($searchCriteria);

		if ($searchResults->getSize()) {
			$result = $searchResults->getItems();
		}
		else {
			$this->searchBuilder->addFilter('increment_id', $refNumber);
			$this->searchBuilder->addFilter('is_parent', 1, 'neq');

			$searchCriteria = $this->searchBuilder->create();
			$searchResults = $this->salesOrder->getList($searchCriteria);

			if ($searchResults->getSize()) {
				$result = $searchResults->getItems();
			}
		}
		return $result;
	}

	/**
	 * Get sub-orders by reference_payment_number
	 *
	 * @param string $refNumber
	 * @return \Magento\Sales\Model\ResourceModel\Order\Collection
	 */
	public function getSubOrders(string $refNumber)
	{
		$this->searchBuilder->addFilter('reference_number', $refNumber);
		$this->searchBuilder->addFilter('is_parent', 1, 'neq');

		$searchCriteria = $this->searchBuilder->create();
		$searchResults = $this->salesOrder->getList($searchCriteria);

		return $searchResults->getItems();
	}

	/**
	 * Get main order by reference_payment_number
	 *
	 * @param string $refNumber
	 * @return \Magento\Sales\Model\ResourceModel\Order\Collection
	 */
	public function getMainOrder(string $refNumber)
	{
		$result = null;
		$this->searchBuilder->addFilter('reference_number', $refNumber);
		$this->searchBuilder->addFilter('is_parent', 1, 'eq');

		$searchCriteria = $this->searchBuilder->create();
		$searchResults = $this->salesOrder->getList($searchCriteria);

		$item = $searchResults->getItems();
		$key = array_keys($item);
		
		if(isset($key[0])) {
			$result = $item[$key[0]];
		} else {
			$collection = $this->orderCollection->create();
			$collection->addFieldToFilter('increment_id', $refNumber);
			$collection->setPageSize(1);

			if(!$collection->getSize()) {
				throw new \Exception('No main order data found with reference number ' . $refNumber);
			}
			
			$order = $collection->getFirstItem();
			$mainOrderId = $order->getData('parent_order');

			try {
				$mainOrder = $this->salesOrder->get($mainOrderId);
				$result = $mainOrder;
			} catch (NoSuchEntityException $e) {
				throw new \Exception('No main order data found with reference number ' . $refNumber . '. ' . $e->getMesage());
			}
		}

		return $result; 
	}

	/**
	 * get sales order by increment id
	 * 
	 * @param string $incrementId
	 * @return \Magento\Sales\Api\Data\OrderInterface
	 */
	public function getOrderByIncrementId(string $incrementId)
	{
		$collection = $this->orderCollection->create();
		$collection->addFieldToFilter('increment_id', $incrementId);
		$collection->setPageSize(1);

		if(!$collection->getSize()) {
			throw new NoSuchEntityException(__('No main order data found with increment id ' . $incrementId));
		}
		
		return $collection->getFirstItem();
	}

	/**
	 * Get sub-orders grand total
	 * @param \Magento\Sales\Model\ResourceModel\Order\Collection $orders
	 * @return float
	 */
	public function getSubOrdersGrandTotal($orders)
	{
		$grandTotal = 0;
		if($orders) {
			foreach($orders as $order) {
				$subtotal = $order->getGrandTotal();
				$grandTotal += $subtotal;
			}
		}

		return round($grandTotal, 2);
	}

	/**
	 * Get sub-orders service fee
	 * @param \Magento\Sales\Model\ResourceModel\Order\Collection $orders
	 * @return float
	 */
	public function getSubOrdersServiceFee($orders)
	{
		$serviceFee = 0;
		if($orders) {
			foreach($orders as $order) {
				if($order->getData('service_fee')) {
					$amount = $order->getData('service_fee');
					$serviceFee += $amount;
				}
			}
		}

		return round($serviceFee, 2);
	}

	/**
	 * Get status
	 * @param \Magento\Sales\Model\ResourceModel\Order\Collection $orders
	 * @return string
	 */
	public function getStatusOrders($orders)
	{
		$status = null;
		if($orders) {
			foreach($orders as $order) {
				if($order->getData('status')) {
					$status = $order->getData('status');
				}
				break;
			}
		}

		return $status;
	}
}
