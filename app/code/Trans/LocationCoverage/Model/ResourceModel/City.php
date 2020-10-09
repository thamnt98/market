<?php
/**
 * Copyright © 2018 EaDesign by Eco Active S.R.L. All rights reserved.
 * See LICENSE for license details.
 */

namespace Trans\LocationCoverage\Model\ResourceModel;

use Trans\LocationCoverage\Api\Data\CityInterface;
use Trans\LocationCoverage\Setup\InstallSchema;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

/**
 * Class City
 * @package Trans\LocationCoverage\Model\ResourceModel
 */
class City extends AbstractDb
{
    /**
     * City constructor.
     * @param Context $context
     * @param string|null $connectionName
     */
    public function __construct(
        Context $context,
        string $connectionName = null
    ) {
        parent::__construct(
            $context,
            $connectionName
        );
    }

    public function _construct()
    {
        $this->_init(InstallSchema::TABLE, CityInterface::ENTITY_ID);
    }
}