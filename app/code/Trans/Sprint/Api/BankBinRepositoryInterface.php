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
use Trans\Sprint\Api\Data\BankBinInterface;

interface BankBinRepositoryInterface {
	/**
	 * Save page.
	 *
	 * @param \Trans\Sprint\Api\Data\BankBinInterface $bankBin
	 * @return \Trans\Sprint\Api\Data\BankBinInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save(BankBinInterface $bankBin);

	/**
	 * Retrieve bank.
	 *
	 * @param int $binId
	 * @return \Trans\Sprint\Api\Data\BankBinInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getById($binId);

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
	 * @param \Trans\Sprint\Api\Data\BankBinInterface $bank
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function delete(BankBinInterface $bank);

	/**
	 * Delete Sprint Response by ID.
	 *
	 * @param int $binId
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function deleteById($binId);

	
}
