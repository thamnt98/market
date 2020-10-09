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

use Trans\DigitalProduct\Api\Data\DigitalProductOperatorListInterface;
use Trans\DigitalProduct\Model\ResourceModel\DigitalProductOperatorList as DigitalProductOperatorListResourceModel;

/**
 * Class DigitalProductOperatorList
 *
 * @SuppressWarnings(PHPMD)
 */
class DigitalProductOperatorList extends \Magento\Framework\Model\AbstractModel implements DigitalProductOperatorListInterface {
	/**
	 * cache tag
	 *
	 * @var string
	 */
	const CACHE_TAG = 'trans_digitalproduct_operatorlist';

	/**
	 * cache tag
	 *
	 * @var string
	 */
	protected $_cacheTag = 'trans_digitalproduct_operatorlist';

	/**
	 * Prefix of model events names
	 *
	 * @var string
	 */
	protected $_eventPrefix = 'trans_digitalproduct_operatorlist';

	/**
	 * @return void
	 * @SuppressWarnings(PHPMD)
	 */
	protected function _construct() {
		$this->_init(DigitalProductOperatorListResourceModel::class);
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
		return $this->getData(DigitalProductOperatorListInterface::ID);
	}

	/**
	 * @param int $id
	 * @return void
	 */
	public function setId($id) {
		return $this->setData(DigitalProductOperatorListInterface::ID, $id);
	}

	/**
	 * @return int
	 */
	public function getBrandId() {
		return $this->getData(DigitalProductOperatorListInterface::BRAND_ID);
	}

	/**
	 * @param int $brandId
	 * @return void
	 */
	public function setBrandId($brandId) {
		return $this->setData(DigitalProductOperatorListInterface::BRAND_ID, $brandId);
	}

	/**
	 * @return string
	 */
	public function getOperatorName() {
		return $this->getData(DigitalProductOperatorListInterface::OPERATOR_NAME);
	}

	/**
	 * @param string $operatorName
	 * @return void
	 */
	public function setOperatorName($operatorName) {
		return $this->setData(DigitalProductOperatorListInterface::OPERATOR_NAME, $operatorName);
	}

	/**
	 * @return string
	 */
	public function getServiceName() {
		return $this->getData(DigitalProductOperatorListInterface::SERVICE_NAME);
	}

	/**
	 * @param string $serviceName
	 * @return void
	 */
	public function setServiceName($serviceName) {
		return $this->setData(DigitalProductOperatorListInterface::SERVICE_NAME, $serviceName);
	}

	/**
	 * @return string
	 */
	public function getPrefixNumber() {
		return $this->getData(DigitalProductOperatorListInterface::PREFIX_NUMBER);
	}

	/**
	 * @param string $prefixNumber
	 * @return void
	 */
	public function setPrefixNumber($prefixNumber) {
		return $this->setData(DigitalProductOperatorListInterface::PREFIX_NUMBER, $prefixNumber);
	}

	/**
	 * @return string
	 */
	public function getCreatedAt() {
		return $this->getData(DigitalProductOperatorListInterface::CREATED_AT);
	}

	/**
	 * @param string $createdAt
	 * @return void
	 */
	public function setCreatedAt($createdAt) {
		return $this->setData(DigitalProductOperatorListInterface::CREATED_AT, $createdAt);
	}

	/**
	 * @return string
	 */
	public function getUpdatedAt() {
		return $this->getData(DigitalProductOperatorListInterface::UPDATED_AT);
	}

	/**
	 * @param string $updatedAt
	 * @return void
	 */
	public function setUpdatedAt($updatedAt) {
		return $this->setData(DigitalProductOperatorListInterface::UPDATED_AT, $updatedAt);
	}
}