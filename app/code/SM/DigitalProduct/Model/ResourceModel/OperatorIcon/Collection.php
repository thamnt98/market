<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Model\ResourceModel\OperatorIcon;

/**
 * Class Collection
 * @package SM\DigitalProduct\Model\ResourceModel\OperatorIcon
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'operator_icon_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \SM\DigitalProduct\Model\OperatorIcon::class,
            \SM\DigitalProduct\Model\ResourceModel\OperatorIcon::class
        );
    }
}

