<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Sprint\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Trans\Sprint\Api\Data\SprintRefundInterface;

interface SprintRefundRepositoryInterface {
	/**
	 * Save page.
	 *
	 * @param \Trans\Sprint\Api\Data\SprintRefundInterface $sprintRefund
	 * @return \Trans\Sprint\Api\Data\SprintRefundInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save(SprintRefundInterface $sprintRefund);

	/**
	 * Retrieve SprintRefund.
	 *
	 * @param int $sprintRefundId
	 * @return \Trans\Sprint\Api\Data\SprintRefundInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getById($sprintRefundId);

	/**
	 * Retrieve Sprint Refund By transaction no.
	 *
	 * @param int $transNo
	 * @return \Trans\Sprint\Api\Data\SprintRefundInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */

	public function getByTransactionNo($transNo);

	/**
	 * Retrieve pages matching the specified criteria.
	 *
	 * @param SearchCriteriaInterface $searchCriteria
	 * @return \Trans\Sprint\Api\Data\SprintRefundSearchResultsInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getList(SearchCriteriaInterface $searchCriteria);

	/**
	 * Delete Sprint Refund.
	 *
	 * @param \Trans\Sprint\Api\Data\SprintRefundInterface $sprintRefund
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function delete(SprintRefundInterface $sprintRefund);

	/**
	 * Delete Sprint Refund by ID.
	 *
	 * @param int $sprintRefundId
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function deleteById($sprintRefundId);
}
