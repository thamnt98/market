<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Category
 * @package SM\DigitalProduct\Model\ResourceModel
 */
class Category extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sm_digitalproduct_category', 'category_id');
    }
}

