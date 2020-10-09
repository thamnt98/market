<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Product description block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace SM\Catalog\Block\Product\View;

use Magento\Catalog\Model\Product;
use Magento\Framework\Phrase;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Catalog\Block\Product\View\Attributes as AttributesCore;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;

/**
 * Attributes attributes block
 *
 * @api
 * @since 100.0.2
 */
class Attributes extends AttributesCore
{
    /**
     * @var Product
     */
    protected $_product = NULL;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = NULL;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var ProductAttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * Attributes constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     * @param ProductAttributeRepositoryInterface $attributeRepository
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        PriceCurrencyInterface $priceCurrency,
        ProductAttributeRepositoryInterface $attributeRepository,
        array $data = []
    ) {
        $this->attributeRepository = $attributeRepository;

        parent::__construct($context, $registry, $priceCurrency, $data);
    }

    /**
     * Determine if we should display the attribute on the front-end
     *
     * @param \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute
     * @param array $excludeAttr
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function isVisibleOnFrontend(
        \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute,
        array $excludeAttr
    ) {
        if ($attribute->getIsVisibleOnFront()) {
            $allowShowSpecification = false;
            $attributeData = $this->attributeRepository->get($attribute->getAttributeCode());
            if ($attributeData->getShowSpecification() == 2) {
                $allowShowSpecification = true;
            }

            return ($allowShowSpecification && !in_array($attribute->getAttributeCode(), $excludeAttr));
        } else {
            return ($attribute->getIsVisibleOnFront() && !in_array($attribute->getAttributeCode(), $excludeAttr));
        }
    }
}
