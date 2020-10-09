<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author  J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */


namespace Trans\IntegrationCatalog\Model\Source;
use \Trans\IntegrationCatalog\Api\Data\IntegrationProductInterface;
 
class ProductTypeSelection extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource 
{
    public function getAllOptions() {
        if ($this->_options === null) {
            $this->_options = [
                ['label' => __(ucfirst(IntegrationProductInterface::PRODUCT_TYPE_SIMPLE_LABEL)), 'value' => IntegrationProductInterface::PRODUCT_TYPE_SIMPLE_VALUE],
                ['label' => __(ucfirst(IntegrationProductInterface::PRODUCT_TYPE_DIGITAL_LABEL)), 'value' => IntegrationProductInterface::PRODUCT_TYPE_DIGITAL_VALUE]
            ];
        }
        return $this->_options;
    }
}