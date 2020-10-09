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

use Trans\Sprint\Api\Data\BankBinInterface;
use Trans\Sprint\Model\ResourceModel\BankBin as ResourceModel;

/**
 * Class SprintResponse
 *
 * @SuppressWarnings(PHPMD)
 */
class BankBin extends \Magento\Framework\Model\AbstractModel implements BankBinInterface {
	

	/**
	 * Prefix of model events names
	 *
	 * @var string
	 */
	protected $_eventPrefix = BankBinInterface::DEFAULT_PREFIX;

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
		return $this->getData(BankBinInterface::ID);
	}

	/**
	 * @param int $id
	 * @return void
	 */
	public function setId($id) {
		return $this->setData(BankBinInterface::ID, $id);
	}

	/**
	 * @return int
	 */
	public function getBankId() {
		return $this->getData(BankBinInterface::BANK_ID);
	}

	/**
	 * @param int $bankId
	 * @return void
	 */
	public function setBankId($bankId) {
		return $this->setData(BankBinInterface::BANK_ID, $bankId);
	}

	/**
	 * @return int
	 */
	public function getTypeId() {
		return $this->getData(BankBinInterface::TYPE_ID);
	}

	/**
	 * @param int $typeId
	 * @return void
	 */
	public function setTypeId($typeId) {
		return $this->setData(BankBinInterface::TYPE_ID, $typeId);
	}

	/**
	 * @return int
	 */
	public function getBinCode() {
		return $this->getData(BankBinInterface::TYPE_ID);
	}

	/**
	 * @param int $binCode
	 * @return void
	 */
	public function setBinCode($binCode) {
		return $this->setData(BankBinInterface::BIN_CODE, $binCode);
	}
	
	/**
	 * @return string
	 */
	public function getCreatedAt() {
		return $this->getData(BankBinInterface::CREATED_AT);
	}

	/**
	 * @param string $createdAt
	 * @return void
	 */
	public function setCreatedAt($createdAt) {
		return $this->setData(BankBinInterface::CREATED_AT, $createdAt);
	}

	/**
	 * @return string
	 */
	public function getUpdatedAt() {
		return $this->getData(BankBinInterface::UPDATED_AT);
	}

	/**
	 * @param string $updatedAt
	 * @return void
	 */
	public function setUpdatedAt($updatedAt) {
		return $this->setData(BankBinInterface::UPDATED_AT, $updatedAt);
	}

	
}
