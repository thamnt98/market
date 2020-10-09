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

use Trans\MasterPayment\Api\Data\MasterPaymentInterface;
use Trans\MasterPayment\Model\ResourceModel\MasterPayment as ResourceModel;

/**
 * Class MasterPayment
 *
 * @SuppressWarnings(PHPMD)
 */
class MasterPayment extends \Magento\Framework\Model\AbstractModel implements MasterPaymentInterface {

	/**
	 * Prefix of model events names
	 *
	 * @var string
	 */
	protected $_eventPrefix = MasterPaymentInterface::DEFAULT_PREFIX;

	/**
	 * @return void
	 * @SuppressWarnings(PHPMD)
	 */
	protected function _construct() {
		$this->_init(ResourceModel::class);
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->getData(MasterPaymentInterface::ID);
	}

	/**
	 * @param int $id
	 * @return void
	 */
	public function setId($id) {
		return $this->setData(MasterPaymentInterface::ID, $id);
	}

	/**
	 * @return string
	 */
	public function getPaymentId() {
		return $this->getData(MasterPaymentInterface::PAYMENT_ID);
	}

	/**
	 * @param string $paymentId
	 * @return void
	 */
	public function setPaymentId($paymentId) {
		return $this->setData(MasterPaymentInterface::PAYMENT_ID, $paymentId);
	}

	/**
	 * @return string
	 */
	public function getPaymentTitle() {
		return $this->getData(MasterPaymentInterface::PAYMENT_TITLE);
	}

	/**
	 * @param string $paymentTitle
	 * @return void
	 */
	public function setPaymentTitle($paymentTitle) {
		return $this->setData(MasterPaymentInterface::PAYMENT_TITLE, $paymentTitle);
	}

	/**
	 * @return string
	 */
	public function getPaymentMethod() {
		return $this->getData(MasterPaymentInterface::PAYMENT_METHOD);
	}

	/**
	 * @param string $paymentMethod
	 * @return void
	 */
	public function setPaymentMethod($paymentMethod) {
		return $this->setData(MasterPaymentInterface::PAYMENT_METHOD, $paymentMethod);
	}

	/**
	 * @return string
	 */
	public function getPaymentTerms() {
		return $this->getData(MasterPaymentInterface::PAYMENT_TERMS);
	}

	/**
	 * @param string $paymentTerms
	 * @return void
	 */
	public function setPaymentTerms($paymentTerms) {
		return $this->setData(MasterPaymentInterface::PAYMENT_TERMS, $paymentTerms);
	}

	/**
	 * @return string
	 */
	public function getCreatedAt() {
		return $this->getData(MasterPaymentInterface::CREATED_AT);
	}

	/**
	 * @param string $createdAt
	 * @return void
	 */
	public function setCreatedAt($createdAt) {
		return $this->setData(MasterPaymentInterface::CREATED_AT, $createdAt);
	}

	/**
	 * @return string
	 */
	public function getUpdatedAt() {
		return $this->getData(MasterPaymentInterface::UPDATED_AT);
	}

	/**
	 * @param string $updatedAt
	 * @return void
	 */
	public function setUpdatedAt($updatedAt) {
		return $this->setData(MasterPaymentInterface::UPDATED_AT, $updatedAt);
	}

}
