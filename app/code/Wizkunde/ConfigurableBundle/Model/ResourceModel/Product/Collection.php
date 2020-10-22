<?php

namespace Wizkunde\ConfigurableBundle\Model\ResourceModel\Product;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';
    /**
     * Define resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Wizkunde\ConfigurableBundle\Model\Product',
            'Wizkunde\ConfigurableBundle\Model\ResourceModel\CompositeProduct'
        );
    }
}
