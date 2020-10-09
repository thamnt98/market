<?php

namespace SM\LayeredNavigation\Plugin\Category\Model\Layer\Filter;

class Category
{
    /**
     * Get filter name
     *
     * @return \Magento\Framework\Phrase
     */
    public function afterGetName()
    {
        return __('Shop by Category');
    }
}
