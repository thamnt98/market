<?php

/**
 * @category Trans
 * @package  Trans_Mepay
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author  Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2020 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Mepay\Model\Data;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Trans\Mepay\Api\Data\AuthCaptureInterface;
use Trans\Mepay\Model\ResourceModel\AuthCapture as AuthCaptureResourceModel;
use Trans\Mepay\Model\Cron\Transaction\SecondCapture;

/**
 * Class AuthCapture
 *
 * @SuppressWarnings(PHPMD)
 */
class AuthCapture extends \Magento\Framework\Model\AbstractModel implements AuthCaptureInterface
{
	/**
	 * cache tag
	 *
	 * @var string
	 */
	const CACHE_TAG = 'auth_capture_mepay';

	/**
	 * cache tag
	 *
	 * @var string
	 */
	protected $_cacheTag = 'auth_capture_mepay';

	/**
	 * Prefix of model events names
	 *
	 * @var string
	 */
	protected $_eventPrefix = 'auth_capture_mepay';

	protected $secondCapture;

	public function __construct(
        Context $context,
        Registry $registry,
        SecondCapture $secondCapture,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
	){
		$this->secondCapture = $secondCapture;
		parent::__construct(
	        $context,
	        $registry,
	        $resource,
	        $resourceCollection,
	        $data
		);
	}

	/**
	 * @return void
	 * @SuppressWarnings(PHPMD)
	 */
	protected function _construct() {
		$this->_init(AuthCaptureResourceModel::class);
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
		return $this->getData(AuthCaptureInterface::ID);
	}

	/**
	 * @param int $sprintId
	 * @return void
	 */
	public function setId($sprintId) {
		return $this->setData(AuthCaptureInterface::ID, $sprintId);
	}

	/**
	 * @return string
	 */
	public function getReferenceNumber() {
		return $this->getData(AuthCaptureInterface::REFERENCE_NUMBER);
	}

	/**
	 * @param string $refNumber
	 * @return void
	 */
	public function setReferenceNumber($refNumber) {
		return $this->setData(AuthCaptureInterface::REFERENCE_NUMBER, $refNumber);
	}

	/**
	 * @return string
	 */
	public function getReferenceOrderId() {
		return $this->getData(AuthCaptureInterface::REFERENCE_ORDER_ID);
	}

	/**
	 * @param string $refOrderId
	 * @return void
	 */
	public function setReferenceOrderId($refOrderId) {
		return $this->setData(AuthCaptureInterface::REFERENCE_ORDER_ID, $refOrderId);
	}

	/**
	 * @return string
	 */
	public function getStatus() {
		return $this->getData(AuthCaptureInterface::STATUS);
	}

	/**
	 * @param string $status
	 * @return void
	 */
	public function setStatus($status) {
		return $this->setData(AuthCaptureInterface::STATUS, $status);
	}

	/**
	 * @return string
	 */
	public function getCreatedAt() {
		return $this->getData(AuthCaptureInterface::CREATED_AT);
	}

	/**
	 * @param string $createdAt
	 * @return void
	 */
	public function setCreatedAt($createdAt) {
		return $this->setData(AuthCaptureInterface::CREATED_AT, $createdAt);
	}

	/**
	 * @return string
	 */
	public function getUpdatedAt() {
		return $this->getData(AuthCaptureInterface::UPDATED_AT);
	}

	/**
	 * @param string $updatedAt
	 * @return void
	 */
	public function setUpdatedAt($updatedAt) {
		return $this->setData(AuthCaptureInterface::UPDATED_AT, $updatedAt);
	}

   /**
    * @inheritdoc
    */
	 public function send($reffNumber, $adjustmentValue)
	 {
	 	$send = $this->secondCapture->captureRequest($reffNumber, $adjustmentValue);
	 	echo json_encode($send);die();
	 }
}
