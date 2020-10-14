<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author  ilma Dinnia Alghani <ilma.dinnia@transdigital.co.id>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalog\Model\ResourceModel\ProductAssociation;

use Magento\Framework\DB\Select;
use Trans\IntegrationCatalog\Api\Data\ProductAssociationInterface;
use Trans\IntegrationCatalog\Model\ProductAssociation;
use Trans\IntegrationCatalog\Model\ResourceModel\ProductAssociation as ResourceModel;

/**
 * Class Collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = ProductAssociationInterface::ID;

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = ProductAssociationInterface::DEFAULT_EVENT;

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = ProductAssociationInterface::DEFAULT_EVENT;

    /**
     * Define resource model
     *
     * @return void
     * @SuppressWarnings(PHPMD)
     */
    protected function _construct()
    {
        $this->_init(ProductAssociation::class, ResourceModel::class);
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
