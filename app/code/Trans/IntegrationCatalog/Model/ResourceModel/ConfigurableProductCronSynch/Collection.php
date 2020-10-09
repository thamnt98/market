<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\IntegrationCatalog\Model\ResourceModel\ConfigurableProductCronSynch;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Trans\IntegrationCatalog\Api\Data\ConfigurableProductCronSynchInterface as Interfaces;
use Trans\IntegrationCatalog\Model\ConfigurableProductCronSynch as MainModel;
use Trans\IntegrationCatalog\Model\ResourceModel\ConfigurableProductCronSynch as ResourceModel;

class Collection extends AbstractCollection
{
	/**
     * @var string
     */
    protected $_idFieldName = Interfaces::ID;

    protected function _construct()
    {
        $this->_init(MainModel::class, ResourceModel::class);
    }
}
