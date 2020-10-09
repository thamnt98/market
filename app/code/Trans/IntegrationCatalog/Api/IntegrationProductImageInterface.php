<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalog\Api;

interface IntegrationProductImageInterface {

	const CRON_DIRECTORY = "Trans\IntegrationCatalog\Cron";
	/**
	 * Validate data
	 *
	 * @param mixed $dataValue
	 * @return mixed
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function validateProductImage($dataValue);

	/**
	 * Prepare Data
	 *
	 * @param mixed $channel
	 * @return mixed
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function prepareData($channel);

	/**
	 * Save product Image
	 *
	 * @param mixed $dataImage
	 * @return mixed
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function saveProductImage($productImgBySku);

}