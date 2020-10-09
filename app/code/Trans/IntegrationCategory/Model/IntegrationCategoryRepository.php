<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCategory
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCategory\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Trans\IntegrationCategory\Api\Data\IntegrationCategoryInterface;
use Trans\IntegrationCategory\Api\Data\IntegrationCategoryInterfaceFactory;
use Trans\IntegrationCategory\Api\IntegrationCategoryRepositoryInterface;
use Trans\IntegrationCategory\Model\ResourceModel\IntegrationCategory as ResourceModel;
use Trans\IntegrationCategory\Model\ResourceModel\IntegrationCategory\Collection;
use Trans\IntegrationCategory\Model\ResourceModel\IntegrationCategory\CollectionFactory;

/**
 *
 */
class IntegrationCategoryRepository implements IntegrationCategoryRepositoryInterface {

	/**
	 * @var array
	 */
	protected $instances = [];

	/**
	 * @var ResourceModel
	 */
	protected $resource;

	/**
	 * @var IntegrationCategoryCollectionFactory
	 */
	private $collectionFactory;

	/**
	 * @var IntegrationCategoryInterface
	 */
	protected $interface;

	/**
	 * @param integrationCategoryInterfaceFactory $IntegrationCategoryInterface
	 */

	function __construct
	(
		CollectionFactory $collectionFactory,
		IntegrationCategoryInterface $integrationCategoryInterface,
		ResourceModel $resource,
		IntegrationCategoryInterfaceFactory $interface

	) {
		$this->collectionFactory            = $collectionFactory;
		$this->integrationCategoryInterface = $integrationCategoryInterface;
		$this->resource                     = $resource;
		$this->interface                    = $interface;

	}

	/**
	 * {@inheritdoc}
	 */
	public function getById($id) {
		if (!isset($this->instances[$id])) {
			/** @var IntegrationCategoryInterface|\Magento\Framework\Model\AbstractModel $data */
			$data = $this->interface->create();
			$this->resource->load($data, $id);
			if (!$data->getId()) {
				throw new NoSuchEntityException(__('Requested Data Reservation Response doesn\'t exist'));
			}
			$this->instances[$id] = $data;
		}
		return $this->instances[$id];
	}

	/**
	 * {@inheritdoc}
	 */
	public function save(IntegrationCategoryInterface $data) {
		/** @var IntegrationCategoryInterface|\Magento\Framework\Model\AbstractModel $data */
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
	 * {@inheritdoc}
	 */
	public function delete(IntegrationCategoryInterface $data) {
		/** @var IntegrationCategoryInterface|\Magento\Framework\Model\AbstractModel $data */
		$id = $data->getId();

		try {
			unset($this->instances[$id]);
			$this->resource->delete($data);
		} catch (ValidatorException $e) {
			throw new CouldNotSaveException(__($e->getMessage()));
		} catch (\Exception $e) {
			throw new StateException(
				__('Unable to remove data %1', $id)
			);
		}
		unset($this->instances[$id]);
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function loadDataByCategoryParentId($categoryParentId) {
		if (empty($categoryParentId)) {
			throw new StateException(__(
				'Parameter MD are empty !'
			));
		}
		$collection = $this->interface->create()->getCollection();
		$collection->addFieldToFilter(IntegrationCategoryInterface::PIM_ID, $categoryParentId);

		return $collection;
	}

	/**
	 * {@inheritdoc}
	 */
	public function loadDataByPimId($pimId) {
		if (empty($pimId)) {
			throw new StateException(__(
				'Parameter PimId are empty !'
			));
		}
		$collection = $this->interface->create()->getCollection();
		$collection->addFieldToFilter(IntegrationCategoryInterface::PIM_ID, $pimId);

		return $collection->getFirstItem();
	}

	/**
	 * {@inheritdoc}
	 */
	public function loadDataByMagentoEntityId($magentoEntityId) {
		if (empty($magentoEntityId)) {
			throw new StateException(__(
				'Parameter MagentoEntityId are empty !'
			));
		}
		$collection = $this->interface->create()->getCollection();
		$collection->addFieldToFilter(IntegrationCategoryInterface::MAGENTO_ENTITY_ID, $magentoEntityId);

		return $collection->getFirstItem();
	}

	/**
	 * {@inheritdoc}
	 */
	public function loadDataByPimCategoryParentId($pimCategoryParentId) {
		if (empty($pimCategoryParentId)) {
			throw new StateException(__(
				'Parameter MD are empty !'
			));
		}
		$collection = $this->interface->create()->getCollection();
		$collection->addFieldToFilter(IntegrationCategoryInterface::PIM_CATEGORY_PARENT_ID, $pimCategoryParentId);

		return $collection;
	}
}