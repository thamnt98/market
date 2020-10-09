<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software Licenuse Trans\IntegrationCatalog\Model\ResourceModel\ConfigurableProductAttribute as ResourceModel;se (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\IntegrationCatalog\Model;

use Magento\Framework\Model\AbstractModel;
use Trans\IntegrationCatalog\Api\Data\ConfigurableProductCronSynchInterface;
use Trans\IntegrationCatalog\Model\ResourceModel\ConfigurableProductCronSynch as ResourceModel;

class ConfigurableProductCronSynch extends AbstractModel implements ConfigurableProductCronSynchInterface
{
	/**
	 * @inheritdoc
	 */
	protected function _construct()
	{
			$this->_init(ResourceModel::class);
	}

	/**
	 * @inheritdoc
	 */
	public function getCronName()
	{
		return $this->_getData(ConfigurableProductCronSynchInterface::CRON_NAME);
	}

	/**
	 * @inheritdoc
	 */
	public function setCronName($data)
	{
		$this->setData(ConfigurableProductCronSynchInterface::CRON_NAME, $data);
	}

	/**
	 * @inheritdoc
	 */
	public function getCronOffset()
	{
		return $this->_getData(ConfigurableProductCronSynchInterface::CRON_OFFSET);
	}

	/**
	 * @inheritdoc
	 */
	public function setCronOffset($data)
	{
		$this->setData(ConfigurableProductCronSynchInterface::CRON_OFFSET, $data);
	}

	/**
	 * @inheritdoc
	 */
	public function getCronlength()
	{
		return $this->_getData(ConfigurableProductCronSynchInterface::CRON_LENGTH);
	}

	/**
	 * @inheritdoc
	 */
	public function setCronLength($data)
	{
		$this->setData(ConfigurableProductCronSynchInterface::CRON_LENGTH, $data);
	}

	/**
	 * @inheritdoc
	 */
	public function getLastUpdated()
	{
		return $this->_getData(ConfigurableProductCronSynchInterface::LAST_UPDATE);
	}

	/**
	 * @inheritdoc
	 */
	public function setLastUpdated($data)
	{
		$this->setData(ConfigurableProductCronSynchInterface::LAST_UPDATE, $data);
	}
}
