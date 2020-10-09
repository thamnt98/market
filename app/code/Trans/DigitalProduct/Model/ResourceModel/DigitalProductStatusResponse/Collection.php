<?php
/**
 * @category Trans
 * @package  Trans_DigitalProduct
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   hadi <ashadi.sejati@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\DigitalProduct\Model\ResourceModel\DigitalProductStatusResponse;

use Magento\Framework\DB\Select;
use Trans\DigitalProduct\Api\Data\DigitalProductStatusResponseInterface;
use Trans\DigitalProduct\Model\DigitalProductStatusResponse;
use Trans\DigitalProduct\Model\ResourceModel\DigitalProductStatusResponse as ResourceModel;

/**
 * Class Collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = DigitalProductStatusResponseInterface::ID;

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = DigitalProductStatusResponseInterface::DEFAULT_EVENT;

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = DigitalProductStatusResponseInterface::DEFAULT_EVENT;

    /**
     * Define resource model
     *
     * @return void
     * @SuppressWarnings(PHPMD)
     */
    protected function _construct()
    {
        $this->_init(DigitalProductStatusResponse::class, ResourceModel::class);
    }

    /**
     * Get SQL for get record count
     *
     * Extra GROUP BY strip added.
     *
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(Select::GROUP);

        return $countSelect;
    }
}
