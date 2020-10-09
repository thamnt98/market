<?php
/**
 * @category Trans
 * @package  Trans_MasterPayment
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\MasterPayment\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Trans\IntegrationOrder\Api\IntegrationOrderItemRepositoryInterface;
use Trans\MasterPayment\Api\Data\MasterPaymentMatrixAdjustmentInterface;
use Trans\MasterPayment\Api\Data\MasterPaymentMatrixAdjustmentInterfaceFactory;
use Trans\MasterPayment\Api\MasterPaymentMatrixAdjustmentRepositoryInterface;
use Trans\MasterPayment\Helper\Data;

/**
 * Class Data
 */
class DataAssignMatrixAdjustment implements ObserverInterface {

	/**
	 * @var Data $dataHelper
	 */
	protected $dataHelper;

	/**
	 * @param \Trans\IntegrationOrder\Helper\Data $dataHelper
	 */
	public function __construct(
		Data $dataHelper,
		IntegrationOrderItemRepositoryInterface $integrationOrderItemRepositoryInterface,
		OrderRepositoryInterface $orderRepoInterface,
		MasterPaymentMatrixAdjustmentRepositoryInterface $masterPaymentMatrixAdjustmentRepositoryInterface,
		MasterPaymentMatrixAdjustmentInterfaceFactory $masterPaymentMatrixAdjustmentInterfaceFactory
	) {
		$this->dataHelper                                       = $dataHelper;
		$this->integrationOrderItemRepositoryInterface          = $integrationOrderItemRepositoryInterface;
		$this->orderRepoInterface                               = $orderRepoInterface;
		$this->masterPaymentMatrixAdjustmentRepositoryInterface = $masterPaymentMatrixAdjustmentRepositoryInterface;
		$this->masterPaymentMatrixAdjustmentInterfaceFactory    = $masterPaymentMatrixAdjustmentInterfaceFactory;

		$this->logger = $dataHelper->getLogger();
	}

	/**
	 * @param  \Magento\Framework\Event\Observer $observer
	 * @return void
	 */
	public function execute(\Magento\Framework\Event\Observer $observer) {
		$this->logger->info('----------------------- Run Observer ' . __CLASS__ . ' -------------------------');

		$refund       = 0;
		$allocatedQty = 0;
		$orderId      = $observer->getData('order_id');
		try {
			$datas = $this->integrationOrderItemRepositoryInterface->getByOrderId($orderId);
			$order = $this->orderRepoInterface->get($orderId);

			foreach ($datas as $data) {
				$refund       = $refund + $this->getMatrixAdjustmentRefund($data);
				$allocatedQty = $allocatedQty + $data->getQuantityAllocated();
			}

			$refundAmount = $order->getGrandTotal();
			if ($allocatedQty) {
				$refundAmount = $refund;
			}

			$result = [
				MasterPaymentMatrixAdjustmentInterface::TRANSACTION_NO => $orderId,
				MasterPaymentMatrixAdjustmentInterface::REFUND_AMOUNT  => $refundAmount,
				MasterPaymentMatrixAdjustmentInterface::PAID_AMOUNT    => $order->getGrandTotal(),
			];

			$this->logger->info("Result data : " . $this->dataHelper->serializeJson($result));

			$this->saveMatrixAdjustment($result);
		} catch (Exception $e) {
			$this->logger->error($e->getMessage());
		} catch (NoSuchEntityException $e) {
			$this->logger->error($e->getMessage());
		}
		$this->logger->info('----------------------- End Observer ' . __CLASS__ . ' -------------------------');

		return $this;
	}

	/**
	 * [getMatrixAdjustmentRefund description]
	 * @param  [type] $data [description]
	 * @return decimal       [description]
	 */
	protected function getMatrixAdjustmentRefund($data) {
		$finalQty = $data->getQty() - $data->getQuantityAllocated();

		$refund = $data->getSubtotal();
		if ($data->getQuantityAllocated() > 0) {
			$refund = $data->getSellPrice() * $finalQty;
		}

		return $refund;
	}

	protected function saveMatrixAdjustment($result) {
		$this->logger->info(' Run ' . __FUNCTION__);
		try {
			$matrixData = $this->masterPaymentMatrixAdjustmentRepositoryInterface->getByTransactionNo($result[MasterPaymentMatrixAdjustmentInterface::TRANSACTION_NO]);
			$matrixData->addData($result);
		} catch (NoSuchEntityException $e) {
			$matrixData = $this->masterPaymentMatrixAdjustmentInterfaceFactory->create();
			$matrixData->setData($result);
		}
		$this->logger->info("Update or save data : " . $this->dataHelper->serializeJson($matrixData->getData()));

		$this->masterPaymentMatrixAdjustmentRepositoryInterface->save($matrixData);

		$this->logger->info(' End ' . __FUNCTION__);
		return true;
	}
}