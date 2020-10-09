<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCategory
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCategory\Model\ResourceModel\IntegrationJob;

use Trans\IntegrationCategory\Model\IntegrationJob;
use Trans\Integration\Api\Data\IntegrationChannelInterface;
use Trans\IntegrationCategory\Api\Data\IntegrationJobInterface;
use Trans\IntegrationCategory\Model\ResourceModel\IntegrationJob as ResourceModel;
use Magento\Framework\DB\Select;

/**
 * Class Collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = IntegrationJobInterface::ID;

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
        $this->_init(IntegrationJob::class, ResourceModel::class);
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