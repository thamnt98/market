<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Model\ResourceModel;

/**
 * Class CategoryContent
 * @package SM\DigitalProduct\Model\ResourceModel
 */
class CategoryContent extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sm_digitalproduct_category_store', 'category_store_id');
    }
}

