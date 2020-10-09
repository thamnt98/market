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
namespace Trans\MasterPayment\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Trans\MasterPayment\Api\MasterPaymentRepositoryInterface;
use Trans\MasterPayment\Api\PaymentInterface;

/**
 * Class Payment
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Payment implements PaymentInterface {

	/**
	 * @var OrderInterface
	 */
	protected $orderInterface;

	/**
	 * @var MasterPaymentRepositoryInterface
	 */
	protected $masterPaymentRepositoryInterface;

	/**
	 * @param OrderInterface                   $orderInterface
	 * @param MasterPaymentRepositoryInterface $masterPaymentRepositoryInterface
	 */
	public function __construct(
		OrderInterface $orderInterface,
		MasterPaymentRepositoryInterface $masterPaymentRepositoryInterface
	) {
		$this->orderInterface                   = $orderInterface;
		$this->masterPaymentRepositoryInterface = $masterPaymentRepositoryInterface;
	}

	/**
	 * @param string $incrementId
	 * @return array
	 */
	public function getMasterPaymentData($incrementId) {

		$order   = $this->orderInterface->loadByIncrementId($incrementId);
		$payment = $order->getPayment();

		if (!$payment) {
			throw new NoSuchEntityException(__('Order doesn\'t exist'));
		}

		$paymentMethod = $payment->getMethod();
		$paymentTerms  = $order->getSprintTermChannelid();

		$dataMasterPayment = $this->masterPaymentRepositoryInterface->getPaymentId($paymentMethod, $paymentTerms);

		$data = [
			"payment_id"     => $dataMasterPayment->getPaymentId(),
			"payment_title"  => $dataMasterPayment->getPaymentTitle(),
			"payment_method" => $dataMasterPayment->getPaymentMethod(),
			"payment_terms"  => $dataMasterPayment->getPaymentTerms(),
		];

		return $data;
	}
}