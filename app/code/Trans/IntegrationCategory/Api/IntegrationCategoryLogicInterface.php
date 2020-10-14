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

interface IntegrationCategoryLogicInterface {
	/**
	 * Save data
	 *
	 * @param mixed $data
	 * @return mixed
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function saveCategory($datas);

	/**
	 * Prepare Data
	 *
	 * @param mixed $channel
	 * @return mixed
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function prepareData($channel);
}