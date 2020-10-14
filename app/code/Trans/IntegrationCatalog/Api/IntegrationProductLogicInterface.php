<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalog\Api;

interface IntegrationProductLogicInterface {

	/**
	 * Save data
	 *
	 * @param mixed $data
	 * @return mixed
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function saveProduct($datas);

	/**
	 * Prepare Data
	 *
	 * @param mixed $channel
	 * @return mixed
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function prepareData($channel);

	/**
	 * Prepare Data Configurable
	 *
	 * @return array
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function prepareDataConfigurable();

	/**
	 * Prepare Data Configurable
	 * 
	 * @param array $data Product Configurable
	 * @return mixed
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function saveDataConfigurable($dataProductConfigurable);

	
}