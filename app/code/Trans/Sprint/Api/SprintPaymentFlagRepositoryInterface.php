<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\Sprint\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Trans\Sprint\Api\Data\SprintPaymentFlagInterface;

interface SprintPaymentFlagRepositoryInterface {
	/**
	 * Save page.
	 *
	 * @param \Trans\Sprint\Api\Data\SprintPaymentFlagInterface $sprintPaymentFlag
	 * @return \Trans\Sprint\Api\Data\SprintPaymentFlagInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save(SprintPaymentFlagInterface $sprintPaymentFlag);

	/**
	 * Retrieve SprintResponse.
	 *
	 * @param int $sprintPaymentFlagId
	 * @return \Trans\Sprint\Api\Data\SprintPaymentFlagInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getById($sprintPaymentFlagId);

	/**
	 * Retrieve Sprint Response By transaction no.
	 *
	 * @param int $transNo
	 * @return \Trans\Sprint\Api\Data\SprintPaymentFlagInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getByTransactionNo($transNo);

	/**
	 * Retrieve pages matching the specified criteria.
	 *
	 * @param SearchCriteriaInterface $searchCriteria
	 * @return \Trans\Sprint\Api\Data\SprintResponseSearchResultsInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getList(SearchCriteriaInterface $searchCriteria);

	/**
	 * Delete Sprint Response.
	 *
	 * @param \Trans\Sprint\Api\Data\SprintPaymentFlagInterface $sprintPaymentFlag
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function delete(SprintPaymentFlagInterface $sprintPaymentFlag);

	/**
	 * Delete Sprint Response by ID.
	 *
	 * @param int $sprintPaymentFlagId
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function deleteById($sprintPaymentFlagId);
}
