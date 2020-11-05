<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalog\Helper;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;
    
    /**
     * @var \Magento\Swatches\Helper\Data
     */
    protected $swatchHelper;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute
     */
    protected $eavAttribute;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Swatches\Helper\Data $swatchHelper
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Swatches\Helper\Data $swatchHelper,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute
    ) {
        parent::__construct($context);
        $this->eavConfig = $eavConfig;
        $this->swatchHelper = $swatchHelper;
        $this->eavAttribute = $eavAttribute;
    }

    /**
     * check is swatch attribute 
     * 
     * @param string $attrCode
     * @return bool 
     */
    public function isSwatchAttr($attrCode)
    {
        $attribute = $this->eavConfig->getAttribute('catalog_product', $attrCode);
        return $this->swatchHelper->isSwatchAttribute($attribute);
    }

    /**
     * get product attribute id by code
     *
     * @param string $code
     * @return int
     */
    public function getProductAttributeId($code)
    {
        return $this->eavAttribute->getIdByCode('catalog_product', $code);
    }

    /**
     * @return string
     */
    public function getBrandAttributeCode()
    {
        return $this->scopeConfig->getValue(
            'amshopby_brand/general/attribute_code',
            ScopeInterface::SCOPE_STORE
        );
    }
}
