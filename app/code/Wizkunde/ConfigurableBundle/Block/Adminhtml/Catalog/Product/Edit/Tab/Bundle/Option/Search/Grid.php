<?php

namespace Wizkunde\ConfigurableBundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option\Search;

class Grid extends \Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option\Search\Grid
{
    /**
     * Apply sorting and filtering to collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_productFactory->create()->getCollection()->setOrder(
            'id'
        )->addAttributeToSelect(
            'name'
        )->addAttributeToSelect(
            'sku'
        )->addAttributeToSelect(
            'price'
        )->addAttributeToSelect(
            'attribute_set_id'
        )->addAttributeToFilter(
            'entity_id',
            ['nin' => $this->_getSelectedProducts()]
        )->addAttributeToFilter(
            'type_id',
            ['in' => $this->getAllowedSelectionTypes()]
        );

        if ($this->getFirstShow()) {
            $collection->addIdFilter('-1');
            $this->setEmptyText(__('What are you looking for?'));
        }

        $this->setCollection($collection);

        return $this;
    }
}
