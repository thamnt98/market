<?php
/**
 * @category Trans
 * @package  Trans_IntegrationEntity
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   HaDi <ashadi.sejati@transdigital.co.id>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * https://ctcorpdigital.com/
 */

namespace Trans\IntegrationEntity\Model\ResourceModel\IntegrationProductAttributeSet;

use Trans\IntegrationEntity\Model\IntegrationJob;
use Trans\Integration\Api\Data\IntegrationChannelInterface;
use Trans\IntegrationEntity\Api\Data\IntegrationProductAttributeSetInterface;
use Trans\IntegrationEntity\Model\ResourceModel\IntegrationProductAttributeSet as ResourceModel;
use Trans\IntegrationEntity\Model\IntegrationProductAttributeSet;
use Magento\Framework\DB\Select;

/**
 * Class Collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = IntegrationProductAttributeSetInterface::ID;

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
        $this->_init(IntegrationProductAttributeSet::class, ResourceModel::class);
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