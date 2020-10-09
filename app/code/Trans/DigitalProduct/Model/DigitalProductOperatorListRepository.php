<?php
/**
 * @category Trans
 * @package  Trans_DigitalProduct
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\DigitalProduct\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Trans\DigitalProduct\Api\Data\DigitalProductOperatorListInterface;
use Trans\DigitalProduct\Api\Data\DigitalProductOperatorListInterfaceFactory;
use Trans\DigitalProduct\Api\Data\DigitalProductOperatorListSearchResultsInterfaceFactory;
use Trans\DigitalProduct\Api\DigitalProductOperatorListRepositoryInterface;
use Trans\DigitalProduct\Model\ResourceModel\DigitalProductOperatorList as ResourceModel;
use Trans\DigitalProduct\Model\ResourceModel\DigitalProductOperatorList\Collection;
use Trans\DigitalProduct\Model\ResourceModel\DigitalProductOperatorList\CollectionFactory;

/**
 * Class DigitalProductOperatorListRepository
 */
class DigitalProductOperatorListRepository implements DigitalProductOperatorListRepositoryInterface {
	/**
	 * @var array
	 */
	protected $instances = [];

	/**
	 * @var ResourceModel
	 */
	protected $resource;

	/**
	 * @var CollectionFactory
	 */
	protected $collectionFactory;

	/**
	 * @var Collection
	 */
	protected $collection;

	/**
	 * @var DigitalProductOperatorListResultsInterfaceFactory
	 */
	protected $searchResultsFactory;

	/**
	 * @var DigitalProductOperatorListInterfaceFactory
	 */
	protected $digitalProductOperatorList;

	/**
	 * @var DataObjectHelper
	 */
	protected $dataObjectHelper;

	/**
	 * @param ResourceModel $resourceModel
	 * @param CollectionFactory $collectionFactory
	 * @param Collection $collection
	 * @param UserStoreResultsInterfaceFactory $searchResultsFactory
	 * @param DigitalProductOperatorListInterfaceFactory $digitalProductOperatorList
	 * @param DataObjectHelper $dataObjectHelper
	 */
	public function __construct(
		ResourceModel $resource,
		Collection $collection,
		CollectionFactory $collectionFactory,
		DigitalProductOperatorListSearchResultsInterfaceFactory $searchResultsFactory,
		DigitalProductOperatorListInterfaceFactory $digitalProductOperatorList,
		DataObjectHelper $dataObjectHelper
	) {
		$this->resource                   = $resource;
		$this->collection                 = $collection;
		$this->collectionFactory          = $collectionFactory;
		$this->searchResultsFactory       = $searchResultsFactory;
		$this->digitalProductOperatorList = $digitalProductOperatorList;
		$this->dataObjectHelper           = $dataObjectHelper;
	}

	/**
	 * {@inheritdoc}
	 */
	public function save(DigitalProductOperatorListInterface $data) {
		/** @var DigitalProductOperatorListInterface|\Magento\Framework\Model\AbstractModel $data */
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
	public function getById($dataId) {
		if (!isset($this->instances[$dataId])) {
			/** @var \Trans\DigitalProduct\Api\Data\DigitalProductOperatorListInterface|\Magento\Framework\Model\AbstractModel $digitalProductOperatorList */
			$data = $this->digitalProductOperatorList->create();
			$this->resource->load($data, $dataId);
			if (!$data->getId()) {
				throw new NoSuchEntityException(__('Requested Data doesn\'t exist'));
			}
			$this->instances[$dataId] = $data;
		}
		return $this->instances[$dataId];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getByBrandId($code) {
		/** @var \Trans\DigitalProduct\Api\Data\DigitalProductOperatorListInterface|\Magento\Framework\Model\AbstractModel $digitalProductOperatorList */
		$collection = $this->collectionFactory->create();

		$collection->addFieldToFilter(DigitalProductOperatorListInterface::CODE, $code);

		$data = $collection->load();

		if ($collection->getSize() < 0) {
			throw new NoSuchEntityException(__('Requested Data doesn\'t exist'));
		}

		$this->instances[$code] = $data;

		return $this->instances[$codepp];
	}

	/**
	 * {@inheritdoc}
	 */
	public function delete(DigitalProductOperatorListInterface $data) {
		/** @var \Trans\DigitalProduct\Api\Data\DigitalProductOperatorListInterface|\Magento\Framework\Model\AbstractModel $digitalProductOperatorList */
		$dataId = $data->getId();
		try {
			unset($this->instances[$dataId]);
			$this->resource->delete($data);
		} catch (ValidatorException $e) {
			throw new CouldNotSaveException(__($e->getMessage()));
		} catch (\Exception $e) {
			throw new StateException(
				__('Unable to remove data %1', $dataId)
			);
		}
		unset($this->instances[$dataId]);
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function deleteById($dataId) {
		$data = $this->getById($dataId);
		return $this->delete($data);
	}
}
