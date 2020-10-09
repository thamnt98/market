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
namespace Trans\Sprint\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Trans\Sprint\Api\Data\BankInterface;

interface BankRepositoryInterface {
	/**
	 * Save page.
	 *
	 * @param \Trans\Sprint\Api\Data\BankInterface $bank
	 * @return \Trans\Sprint\Api\Data\BankInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save(BankInterface $bank);

	/**
	 * Retrieve bank.
	 *
	 * @param int $bankId
	 * @return \Trans\Sprint\Api\Data\BankInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getById($bankId);

	/**
	 * Retrieve pages matching the specified criteria.
	 *
	 * @param SearchCriteriaInterface $searchCriteria
	 * @return \Trans\Sprint\Api\Data\bankSearchResultsInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getList(SearchCriteriaInterface $searchCriteria);

	/**
	 * Delete Sprint Response.
	 *
	 * @param \Trans\Sprint\Api\Data\BankInterface $bank
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function delete(BankInterface $bank);

	/**
	 * Delete Sprint Response by ID.
	 *
	 * @param int $bankId
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function deleteById($bankId);

	/**
	 * Get Object Bank By Name.
	 *
	 * @param int $name
	 * @return \Trans\Sprint\Api\Data\BankInterface
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function loadDataByName($name);

	
}
