<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Sprint\Model;

use Trans\Sprint\Api\Data\BankInterface;
use Trans\Sprint\Model\ResourceModel\Bank as ResourceModel;

/**
 * Class SprintResponse
 *
 * @SuppressWarnings(PHPMD)
 */
class Bank extends \Magento\Framework\Model\AbstractModel implements BankInterface {
	

	/**
	 * Prefix of model events names
	 *
	 * @var string
	 */
	protected $_eventPrefix = BankInterface::DEFAULT_PREFIX;

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
		return $this->getData(BankInterface::ID);
	}

	/**
	 * @param int $id
	 * @return void
	 */
	public function setId($id) {
		return $this->setData(BankInterface::ID, $id);
	}

	/**
	 * @return int
	 */
	public function getName() {
		return $this->getData(BankInterface::NAME);
	}

	/**
	 * @param string $name
	 * @return void
	 */
	public function setName($name) {
		return $this->setData(BankInterface::NAME, $name);
	}

	/**
	 * @return int
	 */
	public function getCode() {
		return $this->getData(BankInterface::NAME);
	}

	/**
	 * @param string $code
	 * @return void
	 */
	public function setCode($code) {
		return $this->setData(BankInterface::CODE, $code);
	}

	/**
	 * @return int
	 */
	public function getLabel() {
		return $this->getData(BankInterface::NAME);
	}

	/**
	 * @param string $label
	 * @return void
	 */
	public function setLabel($label) {
		return $this->setData(BankInterface::LABEL, $label);
	}

	
	/**
	 * @return string
	 */
	public function getCreatedAt() {
		return $this->getData(BankInterface::CREATED_AT);
	}

	/**
	 * @param string $createdAt
	 * @return void
	 */
	public function setCreatedAt($createdAt) {
		return $this->setData(BankInterface::CREATED_AT, $createdAt);
	}

	/**
	 * @return string
	 */
	public function getUpdatedAt() {
		return $this->getData(BankInterface::UPDATED_AT);
	}

	/**
	 * @param string $updatedAt
	 * @return void
	 */
	public function setUpdatedAt($updatedAt) {
		return $this->setData(BankInterface::UPDATED_AT, $updatedAt);
	}

	
}
