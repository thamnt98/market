<?php
/**
 * @category Trans
 * @package  Trans_DigitalProduct
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\DigitalProduct\Api;

use Trans\DigitalProduct\Api\Data\DigitalProductTransactionResponseInterface;

/**
 * @api
 */
interface DigitalProductTransactionResponseRepositoryInterface {

	/**
	 * Save page.
	 *
	 * @param \Trans\DigitalProduct\Api\Data\DigitalProductTransactionResponseInterface $transactionResponse
	 * @return \Trans\DigitalProduct\Api\Data\DigitalProductTransactionResponseInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save(DigitalProductTransactionResponseInterface $transactionResponse);

	/**
	 * Retrieve SprintResponse.
	 *
	 * @param int $transactionResponseId
	 * @return \Trans\DigitalProduct\Api\Data\DigitalProductTransactionResponseInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getById($transactionResponseId);

	/**
	 * Delete Digital Product Inquire Response.
	 *
	 * @param \Trans\DigitalProduct\Api\Data\DigitalProductTransactionResponseInterface $transactionResponse
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function delete(DigitalProductTransactionResponseInterface $transactionResponse);

	/**
	 * Delete Digital Product Inquire Response by ID.
	 *
	 * @param int $transactionResponseId
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function deleteById($transactionResponseId);
}