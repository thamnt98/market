<?php
/**
 * @category Trans
 * @package  Trans_Mepay
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\Mepay\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Trans\Mepay\Api\Data\AuthCaptureInterface;

interface AuthCaptureRepositoryInterface
{
	/**
	 * Save page.
	 *
	 * @param \Trans\Mepay\Api\Data\AuthCaptureInterface $authCapture
	 * @return \Trans\Mepay\Api\Data\AuthCaptureInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save(AuthCaptureInterface $authCapture);

	/**
	 * Retrieve AuthCapture.
	 *
	 * @param int $authCaptureId
	 * @return \Trans\Mepay\Api\Data\AuthCaptureInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getById($authCaptureId);

	/**
	 * Retrieve Auth Capture By reference number.
	 *
	 * @param int $refNumber
	 * @return \Trans\Mepay\Api\Data\AuthCaptureInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getByReferenceNumber($refNumber);

	/**
	 * Retrieve Auth Capture By reference order id.
	 *
	 * @param int $refOrderId
	 * @return \Trans\Mepay\Api\Data\AuthCaptureInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getByReferenceOrderId($refOrderId);

	/**
	 * Delete Auth Capture.
	 *
	 * @param \Trans\Mepay\Api\Data\AuthCaptureInterface $authCapture
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function delete(AuthCaptureInterface $authCapture);

	/**
	 * Delete Auth Capture by ID.
	 *
	 * @param int $authCaptureId
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function deleteById($authCaptureId);
}
