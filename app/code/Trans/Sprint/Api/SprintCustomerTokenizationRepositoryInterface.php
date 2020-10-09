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
namespace Trans\Sprint\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Trans\Sprint\Api\Data\SprintCustomerTokenizationInterface;

interface SprintCustomerTokenizationRepositoryInterface
{	
	/**
	 * Save data.
	 *
	 * @param \Trans\Sprint\Api\Data\SprintCustomerTokenizationInterface $tokenization
	 * @return \Trans\Sprint\Api\Data\SprintCustomerTokenizationInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save(SprintCustomerTokenizationInterface $tokenization);

	/**
	 * Retrieve data by id.
	 *
	 * @param int $tokenId
	 * @return \Trans\Sprint\Api\Data\SprintCustomerTokenizationInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getById($tokenId);

	/**
	 * Retrieve data by customer id.
	 *
	 * @param int $customerId
	 * @return \Trans\Sprint\Api\Data\SprintCustomerTokenizationInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getByCustomerId($customerId);

	/**
	 * Retrieve data by masked cc no.
	 *
	 * @param string $maskedCardNo
	 * @return \Trans\Sprint\Api\Data\SprintCustomerTokenizationInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getByMaskedCardNo($maskedCardNo);

	/**
	 * Check customer's card token data.
	 *
	 * @param int $customerId
	 * @param string $maskedCard
	 * @return bool
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function isCardTokenExists(int $customerId, string $maskedCard);

	/**
	 * Retrieve pages matching the specified criteria.
	 *
	 * @param SearchCriteriaInterface $searchCriteria
	 * @return \Trans\Sprint\Api\Data\bankSearchResultsInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getList(SearchCriteriaInterface $searchCriteria);

	/**
	 * Delete data.
	 *
	 * @param \Trans\Sprint\Api\Data\SprintCustomerTokenizationInterface $token
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function delete(SprintCustomerTokenizationInterface $token);

	/**
	 * Delete data by ID.
	 *
	 * @param int $tokenId
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function deleteById($tokenId);

	/**
	 * save card token
	 * @param string $transactionNo
	 * @param string $maskedCardNo
	 * @param string $cardToken
	 * @return \Trans\Sprint\Api\Data\SprintCustomerTokenizationInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function saveCardToken(string $transactionNo, string $maskedCardNo, string $cardToken);
}
