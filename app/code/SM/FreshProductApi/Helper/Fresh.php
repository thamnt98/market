<?php
/**
 * SM\FreshProductApi\Helper
 *
 * @copyright Copyright © 2020 SmartOSC. All rights reserved.
 * @author    hungnv6@smartosc.com
 */

namespace SM\FreshProductApi\Helper;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use SM\FreshProductApi\Api\Data\FreshProductInterface;
use SM\FreshProductApi\Api\Data\FreshProductInterfaceFactory;
use SM\FreshProduct\Helper\Data as HelperData;

/**
 * Class Populate
 * @package SM\FreshProductApi\Helper
 */
class Fresh extends AbstractHelper
{
    private $product;

    /**
     * @var FreshProductInterfaceFactory
     */
    protected $freshProductDataFactory;
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var HelperData
     */
    protected $freshHelper;

    /**
     * Populate constructor.
     * @param Context $context
     * @param FreshProductInterfaceFactory $freshProductDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param HelperData $freshHelper
     */
    public function __construct(
        Context $context,
        FreshProductInterfaceFactory $freshProductDataFactory,
        DataObjectHelper $dataObjectHelper,
        HelperData $freshHelper
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->freshProductDataFactory = $freshProductDataFactory;
        $this->freshHelper = $freshHelper;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return $this
     */
    public function setProduct($product)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @return array|null[]
     */
    public function toDataArray()
    {
        return [
            FreshProductInterface::OWN_COURIER => $this->validateAttribute(FreshProductInterface::OWN_COURIER) ?? false,
            FreshProductInterface::BASE_PRICE_IN_KG => $this->validateAttribute(FreshProductInterface::BASE_PRICE_IN_KG),
            FreshProductInterface::PROMO_PRICE_IN_KG => $this->validateAttribute(FreshProductInterface::PROMO_PRICE_IN_KG),
            FreshProductInterface::IS_DECIMAL => $this->validateAttribute(FreshProductInterface::IS_DECIMAL) ?? false,
            FreshProductInterface::WEIGHT => $this->getProduct()->getWeight(),
            FreshProductInterface::SOLD_IN => $this->validateAttribute(FreshProductInterface::SOLD_IN),
            FreshProductInterface::PRICE_IN_KG => $this->validateAttribute(FreshProductInterface::PRICE_IN_KG) ?? false,
            FreshProductInterface::TOOLTIP => $this->freshHelper->getTooltip(),
        ];
    }

    /**
     * @param string $attributeCode
     * @return mixed|null
     */
    public function validateAttribute($attributeCode)
    {
        $attribute = $this->getProduct()->getCustomAttribute($attributeCode);
        if ($attribute) {
            return $attribute->getValue();
        }
        return null;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return FreshProductInterface
     */
    public function populateObject($product)
    {
        $this->setProduct($product);
        $data = $this->toDataArray();
        $freshProductData = $this->freshProductDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $freshProductData,
            $data,
            FreshProductInterface::class
        );
        return $freshProductData;
    }
}
