<?php
/**
 * Class ConfigurableStock
 * @package SM\Inventory\Helper
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Inventory\Helper;

use Magento\Framework\Exception\NoSuchEntityException;
use \Magento\Catalog\Model\Product\Type;

class ConfigurableStock
{
    const BASE_CONFIGURABLE_SKU_SUB_FIX = '001';
    const SIMPLE_PRODUCT_TYPE = Type::TYPE_SIMPLE;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $resourceModel;

    /**
     * ConfigurableStock constructor.
     * @param \Magento\Catalog\Model\ResourceModel\Product $resourceModel
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product $resourceModel
    ) {
        $this->resourceModel = $resourceModel;
    }

    /**
     * @var float|int
     */
    private $requestedSkuQty;

    /**
     * @var string
     */
    private $configurableSkuSuffix;

    /**
     * @param $sku
     * @return false|int
     */
    public function checkIsConfigurableBaseSku($sku)
    {
        $position = strlen($sku) - 3;
        return substr($sku, $position, 3) == self::BASE_CONFIGURABLE_SKU_SUB_FIX;
    }

    /**
     * @param $sku
     * @param $requestedQty
     * @return false|int
     */
    public function checkIsConfigurableStockSku($sku, $requestedQty = null)
    {
        preg_match('/\d\d\d$/', $sku, $quantity);
        if (!$this->checkIsConfigurableBaseSku($sku)
            && count($quantity) == 1
            && $this->checkIsExistBaseProduct($sku)
        ) {
            $this->requestedSkuQty = $quantity[0] * $requestedQty;
            $this->configurableSkuSuffix = $quantity[0];
            return true;
        }
        return false;
    }

    /**
     * @return float|int
     */
    public function getConfigurableSkuSuffix()
    {
        return $this->configurableSkuSuffix;
    }

    /**
     * @return float|int
     */
    public function getBaseQty()
    {
        return $this->requestedSkuQty;
    }

    /**
     * @param $sku
     * @return string|bool
     */
    public function getBaseSku($sku)
    {
        $baseSku = preg_replace('/\d\d\d$/', self::BASE_CONFIGURABLE_SKU_SUB_FIX, $sku);
        return $baseSku != $sku ? $baseSku : false;
    }

    /**
     * @param $sku
     * @return bool
     */
    private function checkIsExistBaseProduct($sku)
    {
        $connection = $this->resourceModel->getConnection();
        $select = $connection->select()
            ->from($this->resourceModel->getEntityTable(), 'type_id')
            ->where('sku = :sku');
        $bind = [':sku' => (string)$sku];

        if ($connection->fetchOne($select, $bind) == self::SIMPLE_PRODUCT_TYPE) {
            return true;
        }

        return false;
    }

    /**
     * @param $sku
     * @return string
     */
    public function getSkuBasic($sku)
    {
        if ($this->checkIsConfigurableStockSku($sku)) {
            if ($baseSku = $this->getBaseSku($sku)) {
                return $baseSku;
            }
        } elseif ($this->checkIsConfigurableBaseSku($sku)) {
            return $sku;
        }

        return '';
    }
}
