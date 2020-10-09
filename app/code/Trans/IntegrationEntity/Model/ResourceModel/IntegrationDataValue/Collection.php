<?php
/**
 * @category Trans
 * @package  Trans_IntegrationEntity
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationEntity\Model\ResourceModel\IntegrationDataValue;

use Trans\IntegrationEntity\Model\IntegrationDataValue;
use Trans\Integration\Api\Data\IntegrationChannelInterface;
use Trans\IntegrationEntity\Api\Data\IntegrationDataValueInterface as Interfaces;
use Trans\IntegrationEntity\Model\ResourceModel\IntegrationDataValue as ResourceModel;
use Magento\Framework\DB\Select;

/**
 * Class Collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = Interfaces::ID;

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = IntegrationChannelInterface::DEFAULT_EVENT;
    
    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = IntegrationChannelInterface::DEFAULT_EVENT;

    /**
     * Define resource model
     *
     * @return void
     * @SuppressWarnings(PHPMD)
     */
    protected function _construct()
    {
        $this->_init(IntegrationDataValue::class, ResourceModel::class);
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