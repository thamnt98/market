<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalog\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollection;
use Trans\IntegrationCatalog\Api\Data\IntegrationProductInterface;
use Trans\IntegrationCatalog\Api\Data\IntegrationProductInterfaceFactory;
use Trans\IntegrationCatalog\Api\IntegrationProductRepositoryInterface;
use Trans\IntegrationCatalog\Model\ResourceModel\IntegrationProduct as ResourceModel;
use Trans\IntegrationCatalog\Model\ResourceModel\IntegrationProduct\Collection;
use Trans\IntegrationCatalog\Model\ResourceModel\IntegrationProduct\CollectionFactory;

/**
 *
 */
class IntegrationProductRepository implements IntegrationProductRepositoryInterface {

	/**
	 * @var array
	 */
	protected $instances = [];

	/**
	 * @var ResourceModel
	 */
	protected $resource;

	/**
	 * @var \Magento\Framework\App\ResourceConnection
	 */
	protected $resourceConnection;

	/**
	 * @var ProductCollection
	 */
	protected $productCollection;

	/**
	 * @var \Magento\Catalog\Api\ProductRepositoryInterface
	 */
	protected $productRepository;

	/**
	 * @var IntegrationProductCollectionFactory
	 */
	private $collectionFactory;

	/**
	 * @var IntegrationProductInterface
	 */
	protected $interface;

	/**
	 * @var \Trans\IntegrationCatalog\Helper\Data
	 */
	protected $dataHelper;

	/**
	 * @param \Magento\Framework\App\ResourceConnection $resourceConnection
	 * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
	 * @param CollectionFactory $collectionFactory
	 * @param IntegrationProductInterface $integrationProductInterface
	 * @param ResourceModel $resource
	 * @param IntegrationProductInterfaceFactory $interface
	 * @param \Trans\IntegrationCatalog\Helper\Data $dataHelper
	 */
	function __construct
	(
		\Magento\Framework\App\ResourceConnection $resourceConnection,
		\Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
		ProductCollection $productCollection,
		CollectionFactory $collectionFactory,
		IntegrationProductInterface $integrationProductInterface,
		ResourceModel $resource,
		IntegrationProductInterfaceFactory $interface,
		\Trans\IntegrationCatalog\Helper\Data $dataHelper
	) {
		$this->resourceConnection = $resourceConnection;
		$this->productCollection = $productCollection;
		$this->productRepository = $productRepository;
		$this->collectionFactory = $collectionFactory;
		$this->integrationProductInterface = $integrationProductInterface;
		$this->resource = $resource;
		$this->interface = $interface;
		$this->dataHelper = $dataHelper;

		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/integration_product_repository.log');
        $logger = new \Zend\Log\Logger();
        $this->logger = $logger->addWriter($writer);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getById($id) {
		if (!isset($this->instances[$id])) {
			/** @var IntegrationProductInterface|\Magento\Framework\Model\AbstractModel $data */
			$data = $this->interface->create();
			$this->resource->load($data, $id);
			if (!$data->getId()) {
				throw new NoSuchEntityException(__('Requested Data doesn\'t exist'));
			}
			$this->instances[$id] = $data;
		}
		return $this->instances[$id];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getByEntityId($entityId) {
		if (!isset($this->instances[$entityId])) {
			/** @var IntegrationProductInterface|\Magento\Framework\Model\AbstractModel $data */
			$data = $this->interface->create();
			$this->resource->load($data, $entityId, IntegrationProductInterface::MAGENTO_ENTITY_ID);
			if (!$data->getId()) {
				throw new NoSuchEntityException(__('Requested Data doesn\'t exist'));
			}
			$this->instances[$entityId] = $data;
		}
		return $this->instances[$entityId];
	}

	/**
	 * {@inheritdoc}
	 */
	public function save(IntegrationProductInterface $data) {
		/** @var IntegrationProductInterface|\Magento\Framework\Model\AbstractModel $data */
		try {
			$this->logger->info(print_r($data->getData(), true));
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
	public function delete(IntegrationProductInterface $data) {
		/** @var IntegrationProductInterface|\Magento\Framework\Model\AbstractModel $data */
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
	public function loadDataByProductParentId($productParentId) {
		if (empty($productParentId)) {
			throw new StateException(__(
				'Parameter MD are empty !'
			));
		}
		$collection = $this->interface->create()->getCollection();
		$collection->addFieldToFilter(IntegrationProductInterface::PIM_ID, $productParentId);

		return $collection;
	}

	/**
	 * {@inheritdoc}
	 */
	public function loadDataByPimId($pimId) {
		if (empty($pimId)) {
			throw new StateException(__(
				'Parameter Product PimId are empty !'
			));
		}
		$collection = $this->interface->create()->getCollection();
		$collection->addFieldToFilter(IntegrationProductInterface::PIM_ID, $pimId);

		return $collection->getFirstItem();
	}

	/**
	 * {@inheritdoc}
	 */
	public function loadDataByItemId($itemId) {
		if (empty($itemId)) {
			throw new StateException(__(
				'Parameter Item Id are empty !'
			));
		}
		$collection = $this->interface->create()->getCollection();
		$collection->addFieldToFilter(IntegrationProductInterface::ITEM_ID, $itemId);

		return $collection;
	}

	/**
	 * {@inheritdoc}
	 */
	public function loadDataByPimSku($sku) {
		if (empty($sku)) {
			throw new StateException(__(
				'Parameter Item Id are empty !'
			));
		}
		$collection = $this->interface->create()->getCollection();
		$collection->addFieldToFilter(IntegrationProductInterface::PIM_SKU, $sku);
		$result = null;
		if ($collection->getSize() > 0) {
			return $collection->getFirstItem();
		}
		return $result;
	}

	/**
	 * {@inheritdoc}
	 */
	public function loadSkuConfigurableByMultiStatus($status=[]) {
		if (!is_array($status)) {
			throw new StateException(__(
				'Error Parameter Status mustbe array !'
			));
		}
		$check = array_filter($status);
		if (empty($check)) {
			throw new StateException(__(
				'Error Parameter Status are empty!'
			));
		}
		$collection = $this->interface->create()->getCollection();
		$collection->addFieldToFilter(IntegrationProductInterface::STATUS_CONFIGURABLE, 
			array(
				'in' => $status
			)
		);
		$collection->setPageSize(50);
		
		return $collection;
	}

	/**
	 * check posibility to create configurable product by SKU
	 *
	 * @param string $sku
	 * @return bool
	 */
	public function checkPosibilityConfigurable($sku, $changeVisibility = true)
	{
		$itemId = substr($sku, 0, 8);

		$collection = $this->collectionFactory->create();
		$collection->addFieldToFilter(IntegrationProductInterface::ITEM_ID, ['eq' => $itemId]);
		$collection->addFieldToFilter(IntegrationProductInterface::STATUS_CONFIGURABLE, 0);
		$collection->addFieldToFilter(IntegrationProductInterface::PIM_SKU, ['neq' => $sku]);
		
		$this->logger->info('Configurable size : ' . $collection->getSize());
		$this->logger->info('Item ID : ' . $itemId);
		$this->logger->info('SKU : ' . $sku);

		if($collection->getSize()) {
			if($changeVisibility) {
				if($collection->getSize() == 1) {
					$data = $collection->getFirstItem();
					$sku = $data->getPimSku();
					try {
						$this->changeProductVisibility($sku, IntegrationProductInterface::VISIBILITY_NOT_VISIBLE);
					} catch (\Exception $e) {
						$this->logger->info($e->getMessage());
						$this->logger->info($e->getTraceAsString());
					}
				}
			}
			return true;
		}

		return false;
	}

	/**
	 * change product visibility by SKU
	 *
	 * @param string $sku
	 * @param string $visibility
	 * @return void
	 */
	public function changeProductVisibility($sku, $visibility)
	{
		try {
			$visibilityAttribute = $this->dataHelper->getProductAttributeId('visibility');
			$product = $this->productRepository->get($sku);

			if($product instanceof \Magento\Catalog\Api\Data\ProductInterface) {
				$rowId = $product->getRowId();

				if($rowId) {
					$connection = $this->resourceConnection->getConnection();
					$data = ["value" => $visibility]; // Key_Value Pair
					$where = 'row_id = ' . (int)$rowId . ' and attribute_id = ' . (int)$visibilityAttribute;
					
					$tableName = $connection->getTableName("catalog_product_entity_int");
					$connection->update($tableName, $data, $where);
				}
			}
		} catch (\Exception $e) {
			throw new \Exception("Error change product visibility. SKU = " . $sku . '. ' . $e->getMessage());
		}
	}
}
