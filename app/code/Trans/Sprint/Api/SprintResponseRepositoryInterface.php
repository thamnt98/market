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
use Trans\Sprint\Api\Data\SprintResponseInterface;

interface SprintResponseRepositoryInterface {
	/**
	 * Save page.
	 *
	 * @param \Trans\Sprint\Api\Data\SprintResponseInterface $sprintResponse
	 * @return \Trans\Sprint\Api\Data\SprintResponseInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save(SprintResponseInterface $sprintResponse);

	/**
	 * Retrieve SprintResponse.
	 *
	 * @param int $sprintResponseId
	 * @return \Trans\Sprint\Api\Data\SprintResponseInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getById($sprintResponseId);

	/**
	 * Retrieve Sprint Response By quote id.
	 *
	 * @param int $quoteId
	 * @param int $storeId
	 * @return \Trans\Sprint\Api\Data\SprintResponseInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getByQuoteId($quoteId, $storeId = null);

	/**
	 * Retrieve Sprint Response By transaction no.
	 *
	 * @param int $transNo
	 * @return \Trans\Sprint\Api\Data\SprintResponseInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getByTransactionNo($transNo, $storeId = null);

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
	 * @param \Trans\Sprint\Api\Data\SprintResponseInterface $sprintResponse
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function delete(SprintResponseInterface $sprintResponse);

	/**
	 * Delete Sprint Response by ID.
	 *
	 * @param int $sprintResponseId
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function deleteById($sprintResponseId);
}
