<?php
namespace Wizkunde\ConfigurableBundle\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CompositeProduct extends AbstractDb
{
    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('wizkunde_configurablebundle_product', 'id');
    }
}
