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
use Trans\MasterPayment\Api\Data\MasterPaymentMatrixAdjustmentInterface;

interface MasterPaymentMatrixAdjustmentRepositoryInterface {
	/**
	 * Save page.
	 *
	 * @param \Trans\MasterPayment\Api\Data\MasterPaymentMatrixAdjustmentInterface $data
	 * @return \Trans\MasterPayment\Api\Data\MasterPaymentMatrixAdjustmentInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save(MasterPaymentMatrixAdjustmentInterface $data);

	/**
	 * Retrieve MasterPayment.
	 *
	 * @param int $id
	 * @return \Trans\MasterPayment\Api\Data\MasterPaymentMatrixAdjustmentInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getById($id);

	/**
	 * Retrieve MasterPayment Refund By transaction no.
	 *
	 * @param string $transNo
	 * @return \Trans\MasterPayment\Api\Data\MasterPaymentMatrixAdjustmentInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */

	public function getByTransactionNo($transNo);

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
	 * @param \Trans\MasterPayment\Api\Data\MasterPaymentMatrixAdjustmentInterface $data
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function delete(MasterPaymentMatrixAdjustmentInterface $data);

	/**
	 * Delete MasterPayment Refund by ID.
	 *
	 * @param int $id
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function deleteById($id);
}
