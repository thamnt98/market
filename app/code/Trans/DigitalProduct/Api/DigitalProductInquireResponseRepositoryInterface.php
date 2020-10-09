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

use Trans\DigitalProduct\Api\Data\DigitalProductInquireResponseInterface;

/**
 * @api
 */
interface DigitalProductInquireResponseRepositoryInterface {

	/**
	 * Save page.
	 *
	 * @param \Trans\DigitalProduct\Api\Data\DigitalProductInquireResponseInterface $inquireResponse
	 * @return \Trans\DigitalProduct\Api\Data\DigitalProductInquireResponseInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save(DigitalProductInquireResponseInterface $inquireResponse);

	/**
	 * Retrieve SprintResponse.
	 *
	 * @param int $inquireResponseId
	 * @return \Trans\DigitalProduct\Api\Data\DigitalProductInquireResponseInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getById($inquireResponseId);

	/**
	 * Delete Digital Product Inquire Response.
	 *
	 * @param \Trans\DigitalProduct\Api\Data\DigitalProductInquireResponseInterface $inquireResponse
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function delete(DigitalProductInquireResponseInterface $inquireResponse);

	/**
	 * Delete Digital Product Inquire Response by ID.
	 *
	 * @param int $inquireResponseId
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function deleteById($inquireResponseId);
}