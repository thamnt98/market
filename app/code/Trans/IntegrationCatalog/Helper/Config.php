<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Ilma Dinnia Alghani <ilma.dinnia@transdigital.co.id>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\IntegrationCatalog\Helper;

/**
 * Class Config
 */
class Config extends \Magento\Framework\App\Helper\AbstractHelper {

	const TO_DATE = 'product_association_date/general/to_date';

	const CONFIGURABLE_PRODUCT_SYNCH_IMAGE_CRON_LENGTH = 'synch_configurable_product/general/synch_image_cron_total_data';

	const CONFIGURABLE_PRODUCT_SYNCH_IMAGE_CRON_NAME = 'synch_configurable_product/general/synch_image_cron_name';



	/**
	 * Get paid state
	 *
	 * @return string
	 */
	public function getToDate() {
		return $this->scopeConfig->getValue(self::TO_DATE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * Get Configurable product synch image cron length
	 *
	 * @return string
	 */
	public function getCronConfigProductSynchLength() {
		return $this->scopeConfig->getValue(self::CONFIGURABLE_PRODUCT_SYNCH_IMAGE_CRON_LENGTH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * Get Configurable product synch image cron name
	 *
	 * @return string
	 */
	public function getCronConfigProductSynchName() {
		return $this->scopeConfig->getValue(self::CONFIGURABLE_PRODUCT_SYNCH_IMAGE_CRON_NAME, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
}
