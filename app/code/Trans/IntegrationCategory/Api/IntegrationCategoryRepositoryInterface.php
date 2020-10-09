<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCategory
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCategory\Api;

use Magento\Catalog\Api\Data\CategoryInterface;
use Trans\IntegrationCategory\Api\Data\IntegrationCategoryInterface;

interface IntegrationCategoryRepositoryInterface {

	/**
	 * Retrieve data by id
	 *
	 * @param int $id
	 * @return \Trans\IntegrationCategory\Api\Data\IntegrationCCategoryInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getById($id);

	/**
	 * Save Data
	 *
	 * @param \Trans\IntegrationCategory\Api\Data\IntegrationCategoryInterface $data
	 * @return \Trans\IntegrationCategory\Api\Data\IntegrationCCategoryInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */

	public function save(IntegrationCategoryInterface $data);

	/**
	 * Delete data.
	 *
	 * @param \Trans\IntegrationCategory\Api\Data\IntegrationCCategoryInterface $data
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function delete(IntegrationCategoryInterface $data);

	/**
	 * Load Integration Category by Category Parent Id.
	 *
	 * @param mixed $categoryParentId
	 * @return mixed
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */

	public function loadDataByCategoryParentId($categoryParentId);

	/**
	 * Load Integration Category by PIM Id.
	 *
	 * @param mixed $pimId
	 * @return mixed
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function loadDataByPimId($pimId);

	/**
	 * Load Data by Magento Entity Id.
	 *
	 * @param mixed $magentoEntityId
	 * @return mixed
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function loadDataByMagentoEntityId($magentoEntityId);

}