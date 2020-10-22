<?php
/**
 * Associated products collection
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Wizkunde\ConfigurableBundle\Model\ResourceModel\Product\Type\Grouped;

use Magento\GroupedProduct\Model\ResourceModel\Product\Type\Grouped\AssociatedProductsCollection as APC;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AssociatedProductsCollection extends APC
{
    /**
     * @inheritdoc
     */
    public function _initSelect()
    {
        if ($this->isEnabledFlat()) {
            $this->getSelect()->from(
                [self::MAIN_TABLE_ALIAS => $this->getEntity()->getFlatTableName()],
                null
            )->columns(
                ['status' => new \Zend_Db_Expr(ProductStatus::STATUS_ENABLED)]
            );
            $this->addAttributeToSelect($this->getResource()->getDefaultAttributes());
            if ($this->_catalogProductFlatState->getFlatIndexerHelper()->isAddChildData()) {
                $this->getSelect()->where('e.is_child=?', 0);
                $this->addAttributeToSelect(['child_id', 'is_child']);
            }
        } else {
            $this->getSelect()->from([self::MAIN_TABLE_ALIAS => $this->getEntity()->getEntityTable()]);
        }

        $this->setProduct(
            $this->_getProduct()
        )->addAttributeToSelect(
            'name'
        )->addAttributeToSelect(
            'price'
        )->addAttributeToSelect(
            'sku'
        )->addAttributeToFilter(
            'type_id',
            $this->_config->getComposableTypes()
        );

        return $this;
    }
}
