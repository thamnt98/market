<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalogPrice
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   hadi <ashadi.sejati@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalogPrice\Model\ResourceModel\PromotionPrice;

use Magento\Framework\DB\Select;
use Trans\IntegrationCatalogPrice\Api\Data\PromotionPriceInterface;
use Trans\IntegrationCatalogPrice\Model\PromotionPrice;
use Trans\IntegrationCatalogPrice\Model\ResourceModel\PromotionPrice as ResourceModel;

/**
 * Class Collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = PromotionPriceInterface::ID;

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = PromotionPriceInterface::DEFAULT_EVENT;

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = PromotionPriceInterface::DEFAULT_EVENT;

    /**
     * Define resource model
     *
     * @return void
     * @SuppressWarnings(PHPMD)
     */
    protected function _construct()
    {
        $this->_init(PromotionPrice::class, ResourceModel::class);
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