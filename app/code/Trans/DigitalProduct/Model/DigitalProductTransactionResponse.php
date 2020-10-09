<?php
/**
 * @category Trans
 * @package  Trans_DigitalProduct
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\DigitalProduct\Model;

use Trans\DigitalProduct\Api\Data\DigitalProductTransactionResponseInterface;
use Trans\DigitalProduct\Model\ResourceModel\DigitalProductTransactionResponse as DigitalProductTransactionResponseResourceModel;

/**
 * Class DigitalProductTransactionResponse
 *
 * @SuppressWarnings(PHPMD)
 */
class DigitalProductTransactionResponse extends \Magento\Framework\Model\AbstractModel implements DigitalProductTransactionResponseInterface {
	/**
	 * cache tag
	 *
	 * @var string
	 */
	const CACHE_TAG = 'trans_digitalproduct_transaction_response';

	/**
	 * cache tag
	 *
	 * @var string
	 */
	protected $_cacheTag = 'trans_digitalproduct_transaction_response';

	/**
	 * Prefix of model events names
	 *
	 * @var string
	 */
	protected $_eventPrefix = 'trans_digitalproduct_transaction_response';

	/**
	 * @return void
	 * @SuppressWarnings(PHPMD)
	 */
	protected function _construct() {
		$this->_init(DigitalProductTransactionResponseResourceModel::class);
	}

	/**
	 * Get identities
	 *
	 * @return array
	 */
	public function getIdentities() {
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	public function getDefaultValues() {
		$values = [];

		return $values;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->getData(DigitalProductTransactionResponseInterface::ID);
	}

	/**
	 * @param int $id
	 * @return void
	 */
	public function setId($id) {
		return $this->setData(DigitalProductTransactionResponseInterface::ID, $id);
	}

	/**
	 * @return string
	 */
	public function getCustomerId() {
		return $this->getData(DigitalProductTransactionResponseInterface::CUSTOMER_ID);
	}

	/**
	 * @param string $customerId
	 * @return void
	 */
	public function setCustomerId($customerId) {
		return $this->setData(DigitalProductTransactionResponseInterface::CUSTOMER_ID, $customerId);
	}

	/**
	 * @return string
	 */
	public function getOrderId() {
		return $this->getData(DigitalProductTransactionResponseInterface::ORDER_ID);
	}

	/**
	 * @param string $orderId
	 * @return void
	 */
	public function setOrderId($orderId) {
		return $this->setData(DigitalProductTransactionResponseInterface::ORDER_ID, $orderId);
	}

	/**
	 * @return string
	 */
	public function getRequest() {
		return $this->getData(DigitalProductTransactionResponseInterface::REQUEST);
	}

	/**
	 * @param string $request
	 * @return void
	 */
	public function setRequest($request) {
		return $this->setData(DigitalProductTransactionResponseInterface::REQUEST, $request);
	}

	/**
	 * @return string
	 */
	public function getResponse() {
		return $this->getData(DigitalProductTransactionResponseInterface::RESPONSE);
	}

	/**
	 * @param string $response
	 * @return void
	 */
	public function setResponse($response) {
		return $this->setData(DigitalProductTransactionResponseInterface::RESPONSE, $response);
	}

	/**
	 * @return string
	 */
	public function getStatus() {
		return $this->getData(DigitalProductTransactionResponseInterface::STATUS);
	}

	/**
	 * @param string $status
	 * @return void
	 */
	public function setStatus($status) {
		return $this->setData(DigitalProductTransactionResponseInterface::STATUS, $status);
	}

	/**
	 * @return string
	 */
	public function getMessage() {
		return $this->getData(DigitalProductTransactionResponseInterface::MESSAGE);
	}

	/**
	 * @param string $message
	 * @return void
	 */
	public function setMessage($message) {
		return $this->setData(DigitalProductTransactionResponseInterface::MESSAGE, $message);
	}

	/**
	 * @return string
	 */
	public function getCreatedAt() {
		return $this->getData(DigitalProductTransactionResponseInterface::CREATED_AT);
	}

	/**
	 * @param string $createdAt
	 * @return void
	 */
	public function setCreatedAt($createdAt) {
		return $this->setData(DigitalProductTransactionResponseInterface::CREATED_AT, $createdAt);
	}

	/**
	 * @return string
	 */
	public function getUpdatedAt() {
		return $this->getData(DigitalProductTransactionResponseInterface::UPDATED_AT);
	}

	/**
	 * @param string $updatedAt
	 * @return void
	 */
	public function setUpdatedAt($updatedAt) {
		return $this->setData(DigitalProductTransactionResponseInterface::UPDATED_AT, $updatedAt);
	}
}