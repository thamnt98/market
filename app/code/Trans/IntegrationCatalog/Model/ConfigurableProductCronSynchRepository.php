<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalog\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Trans\IntegrationCatalog\Api\ConfigurableProductCronSynchRepositoryInterface;
use Trans\IntegrationCatalog\Api\Data\ConfigurableProductCronSynchInterface;
use Trans\IntegrationCatalog\Api\Data\ConfigurableProductCronSynchInterfaceFactory as modelFactory;
use Trans\IntegrationCatalog\Model\ResourceModel\ConfigurableProductCronSynch as ResourceModel;
use Trans\IntegrationCatalog\Helper\Config;

class ConfigurableProductCronSynchRepository implements ConfigurableProductCronSynchRepositoryInterface
{
	/**
	 * @var \Trans\IntegrationCatalog\Api\Data\ConfigurableProductCronSynchInterfaceFactory
	 */
	protected $model;

	/**
	 * @var \Trans\IntegrationCatalog\Api\Data\ResourceModel\ConfigurableProductCronSynch
	 */
	protected $resource;

	protected $config;

	/**
	 * Constructor method
	 * @param modelFactory $interface
	 * @param ResourceModel $resource
	 * @param Config $config
	 */
	public function __construct(
		modelFactory $model,
		ResourceModel $resource,
		Config $config
	) {
		$this->model = $model;
		$this->resource = $resource;
		$this->config = $config;
	}

	/**
	 * @inheritdoc
	 */
	public function getById(string $id)
	{
			$data = $this->model->create();
			$this->resource->load($data, $id);
			if (!$data->getId()) {
				throw new NoSuchEntityException(__('Requested Data doesn\'t exist'));
			}
			return $data;
	}

  /**
	 * @inheritdoc
	 */
	public function getByName(string $name)
	{
			$data = $this->loadBy(ConfigurableProductCronSynchInterface::CRON_NAME, $name);
			if (!$data->getId()) {
				throw new NoSuchEntityException(__('Requested Data doesn\'t exist'));
			}
			return $data;
	}

  /**
	 * @inheritdoc
	 */
	public function loadBy(string $field, string $value)
	{
		$data = $this->model->create();
		$this->resource->load($data, $value, $field);
		if (!$data->getId()) {
			throw new NoSuchEntityException(__('Requested Data doesn\'t exist'));
		}
		return $data;
	}

	/**
	 * @inheritdoc
	 */
	public function save(ConfigurableProductCronSynchInterface $data)
	{
		try {
			$this->resource->save($data);
		} catch (\Exception $exception) {
			throw new CouldNotSaveException(__(
				'Could not save the data: %1',
				$exception->getMessage()
			));
		}
		return $data;
	}

	/**
	 * @inheritdoc
	 */
	public function delete(ConfigurableProductCronSynchInterface $data)
	{
		try {
			$this->resource->delete($data);
		} catch (ValidatorException $e) {
			throw new CouldNotSaveException(__($e->getMessage()));
		} catch (\Exception $e) {
			throw new StateException(
				__('Unable to remove data %1', $id)
			);
		}
		return true;
	}

}
