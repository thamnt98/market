<?php
/**
 * Class CartProductInteface
 * @package SM\Checkout\Api\Data
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Checkout\Api\Data;

use Magento\Tests\NamingConvention\true\mixed;

interface CartItemDataInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const INSTALLATION_DATA = 'installation_data';
    const PRODUCT_LABEL = 'product_label';
    const PRODUCT_IMAGE = 'product_image';
    const OPTION_LIST = 'option_list';
    const CONFIG_OPTION_LIST = 'config_option_list';
    const PRODUCT_SKU = 'product_sku';
    const PRODUCT_ID  = 'product_id';
    const IS_CHECKED = 'is_checked';
    const SALABLE_QUANTITY = 'salable_quantity';
    const DISCOUNT_PERCENT = 'discount_percent';
    const ORIGINAL_PRICE = 'original_price';
    const GTM_DATA = 'gtm_data';

    /**
     * @return \SM\Checkout\Api\Data\CartItem\OptionListInterface[]|mixed[]
     */
    public function getOptionList();

    /**
     * @return \SM\Checkout\Api\Data\CartItem\OptionListInterface[]|mixed[]
     */
    public function getConfigOptionList();

    /**
     * @param \SM\Checkout\API\Data\CartItem\OptionListInterface[] $configOptionList
     * @return $this
     */
    public function setConfigOptionList($configOptionList);

    /**
     * @return \SM\Checkout\Api\Data\CartItem\InstallationInterface|null
     */
    public function getInstallationData();

    /**
     * @return mixed[]
     */
    public function getProductLabel();

    /**
     * @return string
     */
    public function getImageUrl();

    /**
     * @return string
     */
    public function getProductSku();

    /**
     * @param string $sku
     * @return $this
     */
    public function setProductSku($sku);

    /**
     * @param $data
     * @return $this
     */
    public function setInstallationData($data);

    /**
     * @return boolean
     */
    public function getIsChecked();

    /**
     * @param boolean $data
     * @return $this
     */
    public function setIsChecked($data);

    /**
     * @return int
     */
    public function getProductId();

    /**
     * @param int $data
     * @return $this
     */
    public function setProductId($data);

    /**
     * @return int
     */
    public function getSalableQuantity();

    /**
     * @param int $qty
     * @return $this
     */
    public function setSalableQuantity($qty);

    /**
     * @return float
     */
    public function getDiscountPercent();

    /**
     * @param float $data
     * @return $this
     */
    public function setDiscountPercent($data);

    /**
     * @return float
     */
    public function getOriginalPrice();

    /**
     * @param float $data
     * @return $this
     */
    public function setOriginalPrice($data);

    /**
     * @return \SM\MobileApi\Api\Data\GTM\GTMCartInterface
     */
    public function getGtmData();

    /**
     * @param \SM\MobileApi\Api\Data\GTM\GTMCartInterface $data
     * @return $this
     */
    public function setGtmData($data);
}
