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

namespace Trans\MasterPayment\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Trans\MasterPayment\Api\Data\MasterPaymentInterface;

interface MasterPaymentRepositoryInterface {
	/**
	 * Save page.
	 *
	 * @param \Trans\MasterPayment\Api\Data\MasterPaymentInterface $masterPayment
	 * @return \Trans\MasterPayment\Api\Data\MasterPaymentInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save(MasterPaymentInterface $masterPayment);

	/**
	 * Retrieve MasterPayment.
	 *
	 * @param int $masterPaymentId
	 * @return \Trans\MasterPayment\Api\Data\MasterPaymentInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getById($masterPaymentId);

	/**
	 * Retrieve MasterPayment Refund By transaction no.
	 *
	 * @param int $transNo
	 * @return \Trans\MasterPayment\Api\Data\MasterPaymentInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */

	public function getByTransactionNo($transNo);

	/**
	 * Get Payment Id by
	 *
	 * @param string $paymentMethod
	 * @param int $terms
	 * @return string
	 */
	public function getPaymentId($paymentMethod, $terms = null);

	/**
	 * Retrieve pages matching the specified criteria.
	 *
	 * @param SearchCriteriaInterface $searchCriteria
	 * @return \Trans\MasterPayment\Api\Data\MasterPaymentSearchResultsInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getList(SearchCriteriaInterface $searchCriteria);

	/**
	 * Delete MasterPayment Refund.
	 *
	 * @param \Trans\MasterPayment\Api\Data\MasterPaymentInterface $masterPayment
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function delete(MasterPaymentInterface $masterPayment);

	/**
	 * Delete MasterPayment Refund by ID.
	 *
	 * @param int $masterPaymentId
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function deleteById($masterPaymentId);
}
