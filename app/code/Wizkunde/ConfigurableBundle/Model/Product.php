<?php

namespace Wizkunde\ConfigurableBundle\Model;

use Magento\Framework\Model\AbstractModel;

class Product extends AbstractModel
{
    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Wizkunde\ConfigurableBundle\Model\ResourceModel\CompositeProduct');
    }
}
