<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Model\ResourceModel;

/**
 * Class OperatorIcon
 * @package SM\DigitalProduct\Model\ResourceModel
 */
class OperatorIcon extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sm_digitalproduct_operator_icon', 'operator_icon_id');
    }
}

