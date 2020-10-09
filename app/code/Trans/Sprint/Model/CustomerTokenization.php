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

namespace Trans\Sprint\Model;

use Trans\Sprint\Api\Data\SprintCustomerTokenizationInterface;
use Trans\Sprint\Model\ResourceModel\CustomerTokenization as ResourceModel;

/**
 * Class CustomerTokenization
 *
 * @SuppressWarnings(PHPMD)
 */
class CustomerTokenization extends \Magento\Framework\Model\AbstractModel implements SprintCustomerTokenizationInterface
{
	/**
	 * Prefix of model events names
	 *
	 * @var string
	 */
	protected $_eventPrefix = 'sprint_customer_tokenization';

	/**
	 * @return void
	 * @SuppressWarnings(PHPMD)
	 */
	protected function _construct() {
		$this->_init(ResourceModel::class);
	}

	/**
     * @inheritDoc
     */
	public function getId() {
		return $this->getData(SprintCustomerTokenizationInterface::ID);
	}

	/**
     * @inheritDoc
     */
	public function setId($id) {
		return $this->setData(SprintCustomerTokenizationInterface::ID, $id);
	}

	/**
     * @inheritDoc
     */
	public function getCustomerId() {
		return $this->getData(SprintCustomerTokenizationInterface::CUSTOMER_ID);
	}

	/**
     * @inheritDoc
     */
	public function setCustomerId($customerId) {
		return $this->setData(SprintCustomerTokenizationInterface::CUSTOMER_ID, $customerId);
	}

	/**
     * @inheritDoc
     */
	public function getMaskedCard() {
		return $this->getData(SprintCustomerTokenizationInterface::MASKED_CARD_NO);
	}

	/**
     * @inheritDoc
     */
	public function setMaskedCard($maskedCard) {
		return $this->setData(SprintCustomerTokenizationInterface::MASKED_CARD_NO, $maskedCard);
	}

	/**
     * @inheritDoc
     */
	public function getCardToken() {
		return $this->getData(SprintCustomerTokenizationInterface::CARD_TOKEN);
	}

	/**
     * @inheritDoc
     */
	public function setCardToken($cardToken) {
		return $this->setData(SprintCustomerTokenizationInterface::CARD_TOKEN, $cardToken);
	}
	
	/**
     * @inheritDoc
     */
	public function getCreatedAt() {
		return $this->getData(SprintCustomerTokenizationInterface::CREATED_AT);
	}

	/**
     * @inheritDoc
     */
	public function setCreatedAt($createdAt) {
		return $this->setData(SprintCustomerTokenizationInterface::CREATED_AT, $createdAt);
	}

	/**
     * @inheritDoc
     */
	public function getUpdatedAt() {
		return $this->getData(SprintCustomerTokenizationInterface::UPDATED_AT);
	}

	/**
     * @inheritDoc
     */
	public function setUpdatedAt($updatedAt) {
		return $this->setData(SprintCustomerTokenizationInterface::UPDATED_AT, $updatedAt);
	}
}
