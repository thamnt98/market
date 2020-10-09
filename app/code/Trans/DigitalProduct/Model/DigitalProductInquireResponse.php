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

use Trans\DigitalProduct\Api\Data\DigitalProductInquireResponseInterface;
use Trans\DigitalProduct\Model\ResourceModel\DigitalProductInquireResponse as DigitalProductInquireResponseResourceModel;

/**
 * Class DigitalProductInquireResponse
 *
 * @SuppressWarnings(PHPMD)
 */
class DigitalProductInquireResponse extends \Magento\Framework\Model\AbstractModel implements DigitalProductInquireResponseInterface {
	/**
	 * cache tag
	 *
	 * @var string
	 */
	const CACHE_TAG = 'trans_digitalproduct_inquire_response';

	/**
	 * cache tag
	 *
	 * @var string
	 */
	protected $_cacheTag = 'trans_digitalproduct_inquire_response';

	/**
	 * Prefix of model events names
	 *
	 * @var string
	 */
	protected $_eventPrefix = 'trans_digitalproduct_inquire_response';

	/**
	 * @return void
	 * @SuppressWarnings(PHPMD)
	 */
	protected function _construct() {
		$this->_init(DigitalProductInquireResponseResourceModel::class);
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
		return $this->getData(DigitalProductInquireResponseInterface::ID);
	}

	/**
	 * @param int $id
	 * @return void
	 */
	public function setId($id) {
		return $this->setData(DigitalProductInquireResponseInterface::ID, $id);
	}

	/**
	 * @return string
	 */
	public function getCustomerId() {
		return $this->getData(DigitalProductInquireResponseInterface::CUSTOMER_ID);
	}

	/**
	 * @param string $customerId
	 * @return void
	 */
	public function setCustomerId($customerId) {
		return $this->setData(DigitalProductInquireResponseInterface::CUSTOMER_ID, $customerId);
	}

	/**
	 * @return string
	 */
	public function getRequest() {
		return $this->getData(DigitalProductInquireResponseInterface::REQUEST);
	}

	/**
	 * @param string $request
	 * @return void
	 */
	public function setRequest($request) {
		return $this->setData(DigitalProductInquireResponseInterface::REQUEST, $request);
	}

	/**
	 * @return string
	 */
	public function getResponse() {
		return $this->getData(DigitalProductInquireResponseInterface::RESPONSE);
	}

	/**
	 * @param string $response
	 * @return void
	 */
	public function setResponse($response) {
		return $this->setData(DigitalProductInquireResponseInterface::RESPONSE, $response);
	}

	/**
	 * @return string
	 */
	public function getStatus() {
		return $this->getData(DigitalProductInquireResponseInterface::STATUS);
	}

	/**
	 * @param string $status
	 * @return void
	 */
	public function setStatus($status) {
		return $this->setData(DigitalProductInquireResponseInterface::STATUS, $status);
	}

	/**
	 * @return string
	 */
	public function getMessage() {
		return $this->getData(DigitalProductInquireResponseInterface::MESSAGE);
	}

	/**
	 * @param string $message
	 * @return void
	 */
	public function setMessage($message) {
		return $this->setData(DigitalProductInquireResponseInterface::MESSAGE, $message);
	}

	/**
	 * @return string
	 */
	public function getCreatedAt() {
		return $this->getData(DigitalProductInquireResponseInterface::CREATED_AT);
	}

	/**
	 * @param string $createdAt
	 * @return void
	 */
	public function setCreatedAt($createdAt) {
		return $this->setData(DigitalProductInquireResponseInterface::CREATED_AT, $createdAt);
	}

	/**
	 * @return string
	 */
	public function getUpdatedAt() {
		return $this->getData(DigitalProductInquireResponseInterface::UPDATED_AT);
	}

	/**
	 * @param string $updatedAt
	 * @return void
	 */
	public function setUpdatedAt($updatedAt) {
		return $this->setData(DigitalProductInquireResponseInterface::UPDATED_AT, $updatedAt);
	}
}