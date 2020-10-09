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

use Trans\DigitalProduct\Api\Data\DigitalProductOperatorListInterface;

interface DigitalProductOperatorListRepositoryInterface {
	/**
	 * Save data.
	 *
	 * @param \Trans\DigitalProduct\Api\Data\DigitalProductOperatorListInterface $data
	 * @return \Trans\DigitalProduct\Api\Data\DigitalProductOperatorListInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save(DigitalProductOperatorListInterface $data);

	/**
	 * Retrieve data by id
	 *
	 * @param int $dataId
	 * @return \Trans\DigitalProduct\Api\Data\DigitalProductOperatorListInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getById($dataId);

	/**
	 * Retrieve data by code
	 *
	 * @param string $code
	 * @return \Trans\DigitalProduct\Api\Data\DigitalProductOperatorListInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getByBrandId($code);

	/**
	 * Retrieve pages matching the specified criteria.
	 *
	 * @param \Trans\DigitalProduct\Api\Data\DigitalProductOperatorListInterface $data
	 * @return \Trans\DigitalProduct\Api\Data\DigitalProductOperatorListInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function delete(DigitalProductOperatorListInterface $data);

	/**
	 * Delete data by ID.
	 *
	 * @param int $dataId
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function deleteById($dataId);
}