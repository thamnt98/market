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
namespace Trans\IntegrationCatalog\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Trans\IntegrationCatalog\Api\Data\ConfigurableProductCronSynchInterface as Interfaces;

class ConfigurableProductCronSynch extends AbstractDb
{
	protected function _construct()
	{
		$this->_init(Interfaces::TABLE_NAME, Interfaces::ID);
	}
}
