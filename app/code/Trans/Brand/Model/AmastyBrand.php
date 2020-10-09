<?php
/**
 * @category Trans
 * @package  Trans_AmastyBrand
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.com>
 *
 * Copyright © 2020 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Brand\Model;

use \Amasty\ShopbyBase\Api\Data\OptionSettingInterface;
use \Trans\Brand\Api\Data\AmastyBrandInterface;

class AmastyBrand extends \Amasty\ShopbyBase\Model\OptionSetting implements AmastyBrandInterface {

	/**
	 * @inheritdoc
	 */
	public function getAmastyPimId() {
		return $this->getData(AmastyBrandInterface::AMASTY_PIM_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setAmastyPimId($amastyPimId) {
		return $this->setData(AmastyBrandInterface::AMASTY_PIM_ID, $amastyPimId);
	}

	/**
	 * @inheritdoc
	 */
	public function getAmastyPimCode() {
		return $this->getData(AmastyBrandInterface::AMASTY_PIM_CODE);
	}

	/**
	 * @inheritdoc
	 */
	public function setAmastyPimCode($amastyPimCode) {
		return $this->setData(AmastyBrandInterface::AMASTY_PIM_CODE, $amastyPimCode);
	}

	/**
	 * @inheritdoc
	 */
	public function getOptionSettingId() {
		return $this->getData(OptionSettingInterface::OPTION_SETTING_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setOptionSettingId($optionSettingId) {
		return $this->setData(OptionSettingInterface::OPTION_SETTING_ID, $optionSettingId);
	}
}