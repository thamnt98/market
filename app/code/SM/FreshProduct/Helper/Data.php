<?php

namespace SM\FreshProduct\Helper;

use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_TOOLTIP_CONTENT = 'fresh_product/general/tooltip';

    const IS_FRESH = 'is_fresh';
    const OWN_COURIER = 'is_fresh';
    const BASE_PRICE_IN_KG = 'base_price_in_kg';
    const PROMO_PRICE_IN_KG = 'promo_price_in_kg';
    const IS_DECIMAL = 'is_decimal';
    const SOLD_IN = 'sold_in';
    const PRICE_IN_KG = 'price_in_kg';
    const PACK = '1';
    const YES = '1';
    const NO = '0';

    /**
     * @var PriceHelper
     */
    protected $priceHelper;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    public function __construct(
        PriceHelper $priceHelper,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Request\Http $request
    ) {
        parent::__construct($context);
        $this->priceHelper = $priceHelper;
        $this->request = $request;
    }

    public function isFreshCategory($category)
    {
        $value = false;
        if ($attribute = $category->getCustomAttribute(self::IS_FRESH)) {
            if ($attribute->getValue() == self::YES) {
                $value = true;
            }
        }
        return $value;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return boolean
     */
    public function isFreshProduct($product)
    {
        return $this->getData($product, self::OWN_COURIER);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return boolean
     */
    public function isPriceInKg($product)
    {
        return $this->getData($product, self::PRICE_IN_KG);
    }

    public function getData($product, $attributeCode)
    {
        $value = false;
        if ($product->getData($attributeCode)) {
            if ($product->getData($attributeCode) == self::YES) {
                $value = true;
            }
        }
        return $value;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return float|string
     */
    public function getPricePerKg($product)
    {
        if ($this->getPromoPrice($product) && ($this->getPromoPrice($product) > 0)) {
            $value = $this->getPromoPrice($product);
        } else {
            $value = $this->getBasePrice($product);
        }
        return  $this->formatPrice($value);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getSoldIn($product)
    {
        if ($value = $product->getData(self::SOLD_IN)) {
            return $value;
        }
        return '';
    }

    public function getPromoPrice($product)
    {
        if ($value = $product->getData(self::PROMO_PRICE_IN_KG)) {
            return $value;
        }
        return 0;
    }

    public function getBasePrice($product)
    {
        if ($value = $product->getData(self::BASE_PRICE_IN_KG)) {
            return $value;
        }
        return 0;
    }

    public function formatPrice($value)
    {
        return $this->priceHelper->currency($value, true, false);
    }

    public function isShowPromoPrice($product)
    {
        if ($this->isFreshProduct($product) && $this->isPriceInKg($product)) {
            if (($this->getPromoPrice($product) < $this->getBasePrice($product))
                && ($this->getPromoPrice($product) > 0)
            ) {
                return true;
            }
        }
        return false;
    }

    public function getTooltip()
    {
        return $this->scopeConfig->getValue(
            self::XML_TOOLTIP_CONTENT,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function isCategoryPage()
    {
        $currentUrl = $this->request->getFullActionName();
        if ($currentUrl == 'catalog_category_view') {
            return true;
        }
        return false;
    }
}
