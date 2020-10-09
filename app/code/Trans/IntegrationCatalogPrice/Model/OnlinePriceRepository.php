<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalogPrice
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Hadi <ashadi.sejati@ctcorpdigital.com>
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalogPrice\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Trans\IntegrationCatalogPrice\Api\OnlinePriceRepositoryInterface;
use Trans\IntegrationCatalogPrice\Api\Data\OnlinePriceInterface;
use Trans\IntegrationCatalogPrice\Api\Data\OnlinePriceInterfaceFactory;
use Trans\IntegrationCatalogPrice\Model\ResourceModel\OnlinePrice as ResourceModel;
use Trans\IntegrationCatalogPrice\Model\ResourceModel\OnlinePrice\Collection;
use Trans\IntegrationCatalogPrice\Model\ResourceModel\OnlinePrice\CollectionFactory;
use Magento\InventoryApi\Api\Data\SourceInterfaceFactory as SourceInterface;

/**
 *
 */
class OnlinePriceRepository implements OnlinePriceRepositoryInterface {

	/**
	 * @var array
	 */
	protected $instances = [];

	/**
	 * @var ResourceModel
	 */
	protected $resource;

	/**
	 * @var CatalogPriceCollectionFactory
	 */
	private $collectionFactory;

	/**
	 * @var StorePriceInterface
	 */
	protected $interface;

	/**
	 * @var SourceInterface
	 */
	protected $sourceInterface;

	/**
	 * @param CollectionFactory $collectionFactory
	 * @param OnlinePriceInterface $onlinePriceInterface
	 * @param ResourceModel $resource
	 * @param OnlinePriceInterfaceFactory $interface
	 * @param SourceInterface $sourceInterface
	*/

	function __construct
	(
		CollectionFactory $collectionFactory
		,OnlinePriceInterface $onlinePriceInterface
		,ResourceModel $resource
		,OnlinePriceInterfaceFactory $interface
		,SourceInterface $sourceInterface

	) {
		$this->collectionFactory          = $collectionFactory;
		$this->onlinePriceInterface 	  = $onlinePriceInterface;
		$this->resource                   = $resource;
		$this->interface                  = $interface;
		$this->sourceInterface            = $sourceInterface;

	}

	/**
	 * {@inheritdoc}
	 */
	public function getById($id) {
		if (!isset($this->instances[$id])) {
			/** @var OnlinePriceInterface|\Magento\Framework\Model\AbstractModel $data */
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
	public function save(OnlinePriceInterface $data) {
		/** @var OnlinePriceInterface|\Magento\Framework\Model\AbstractModel $data */
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
	public function delete(OnlinePriceInterface $data) {
		/** @var OnlinePriceInterface|\Magento\Framework\Model\AbstractModel $data */
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
	public function loadDataBySku($sku="") {
		if (empty($sku)) {
			throw new StateException(__(
				'Parameter Sku are empty !'
			));
		}
		
		$collection = $this->interface->create()->getCollection();
		$collection->addFieldToFilter(OnlinePriceInterface::SKU, $sku);

		$getLastCollection =NULL;
		if($collection->getSize()){
            $getLastCollection = $collection->getFirstItem();
            
        }
		return $getLastCollection;
	}
}