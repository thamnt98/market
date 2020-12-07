<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalogPrice
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 * @modify   J.P <jaka.pondan@ctcorpdigital.com>, Anan Fauzi <anan.fauzi@transdigital.co.id>
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalogPrice\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Trans\IntegrationCatalogPrice\Api\StorePriceRepositoryInterface;
use Trans\IntegrationCatalogPrice\Api\Data\StorePriceInterface;
use Trans\IntegrationCatalogPrice\Api\Data\StorePriceInterfaceFactory;
use Trans\IntegrationCatalogPrice\Model\ResourceModel\StorePrice as ResourceModel;
use Trans\IntegrationCatalogPrice\Model\ResourceModel\StorePrice\Collection;
use Trans\IntegrationCatalogPrice\Model\ResourceModel\StorePrice\CollectionFactory;
use Magento\InventoryApi\Api\Data\SourceInterfaceFactory as SourceInterface;

/**
 * \Trans\IntegrationCatalogPrice\Model\StorePriceRepository
 */
class StorePriceRepository implements StorePriceRepositoryInterface
{
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
     * @param StorePriceInterface $storePriceInterface
     * @param ResourceModel $resource
     * @param StorePriceInterfaceFactory $interface
     * @param SourceInterface $sourceInterface
    */

    /**
     * Constructor method
     *
     * @param \Trans\IntegrationCatalogPrice\Model\ResourceModel\StorePrice\CollectionFactory $collectionFactory
     * @param \Trans\IntegrationCatalogPrice\Api\Data\StorePriceInterface $storePriceInterface
     * @param \Trans\IntegrationCatalogPrice\Model\ResourceModel\StorePrice $resource
     * @param \Trans\IntegrationCatalogPrice\Api\Data\StorePriceInterfaceFactory $interface
     * @param \Magento\InventoryApi\Api\Data\SourceInterfaceFactory $sourceInterface
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        StorePriceInterface $storePriceInterface,
        ResourceModel $resource,
        StorePriceInterfaceFactory $interface,
        SourceInterface $sourceInterface
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->storePriceInterface = $storePriceInterface;
        $this->resource = $resource;
        $this->interface = $interface;
        $this->sourceInterface = $sourceInterface;

    }

    /**
     * {@inheritdoc}
     */
    public function getById($id) {
        if (!isset($this->instances[$id])) {
            /** @var StorePriceInterface|\Magento\Framework\Model\AbstractModel $data */
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
    public function loadDataBySku($sku)
    {
        if (empty($sku)) {
            throw new StateException(__(
                'Parameter Sku are empty !'
            ));
        }
        $collection = $this->interface->create()->getCollection();
        $collection->addFieldToFilter(StorePriceInterface::SKU, $sku);

        $getLastCollection = null;
        if ($collection->getSize()) {
            $getLastCollection = $collection->getFirstItem();
        }
        return $getLastCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function loadDataBySkuNStore($sku = "", $store = "")
    {
        if (empty($sku)) {
            throw new StateException(__(
                'Parameter Sku are empty !'
            ));
        }
        if (empty($store)) {
            throw new StateException(__(
                'Parameter Store are empty !'
            ));
        }
        $collection = $this->interface->create()->getCollection();
        $collection->addFieldToFilter(StorePriceInterface::SKU, $sku);
        $collection->addFieldToFilter(StorePriceInterface::SOURCE_CODE, $store);

        $getLastCollection = null;
        if ($collection->getSize()) {
            $getLastCollection = $collection->getFirstItem();
        }
        return $getLastCollection;
    }
    
    /**
     * @return string
     */
    public function loadQueryBySkuNStore($sku = "", $store = "")
    {
        if (empty($sku)) {
            throw new StateException(__(
                'Parameter Sku are empty !'
            ));
        }
        if (empty($store)) {
            throw new StateException(__(
                'Parameter Store are empty !'
            ));
        }
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(StorePriceInterface::SKU, $sku);
        $collection->addFieldToFilter(StorePriceInterface::SOURCE_CODE, $store);
        $collection->getSelect()->limit(1)->order('id desc');
        return $collection->getSelect()->__toString();
    }

    /**
     * {@inheritdoc}
     */
    public function loadDataByStoreAttrCode($code = "", $store = "")
    {
        if (empty($code)) {
            throw new StateException(__(
                'Parameter Store Attr are empty !'
            ));
        }
        if (empty($store)) {
            throw new StateException(__(
                'Parameter Store are empty !'
            ));
        }
        
        $collection = $this->interface->create()->getCollection();
        $collection->addFieldToFilter(StorePriceInterface::STORE_ATTR_CODE, $code);
        $collection->addFieldToFilter(StorePriceInterface::SOURCE_CODE, $store);

        $getLastCollection = null;
        if ($collection->getSize()) {
            $getLastCollection = $collection->getFirstItem();
        }
        return $getLastCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function loadDataByStoreCode($store = "")
    {
        if (empty($store)) {
            throw new StateException(__(
                'Parameter Store are empty !'
            ));
        }
        
        $collection = $this->interface->create()->getCollection();
        $collection->addFieldToFilter(StorePriceInterface::SOURCE_CODE, $store);
        $getLastCollection = null;
        if ($collection->getSize()) {
            $getLastCollection = $collection->getFirstItem();
        }
        return $getLastCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function save(StorePriceInterface $data)
    {
        /** @var StorePriceInterface|\Magento\Framework\Model\AbstractModel $data */
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
    public function delete(StorePriceInterface $data) {
        /** @var StorePriceInterface|\Magento\Framework\Model\AbstractModel $data */
        $id = $data->getId();
        try {
            unset($this->instances[$id]);
            $this->resource->delete($data);
        } catch (\Magento\Framework\Exception\ValidatorException $e) {
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
     * Get Inventory Store
     * @param $sku string
     * @param $store string
     * @return Magento\InventoryApi\Api\Data\SourceItemInterface
     */
    public function getInventoryStore($store)
    {
        $result = null;
        try {
            $collection = $this->sourceInterface->create()->getCollection();
            $collection->addFieldToFilter('source_code', $store);
            if ($collection->getSize()) {
                $result = $collection->getFirstItem();
            }
        } catch (\Exception $e) {
            throw new StateException(
                __('Error : '. $e->getMessage())
            );
        }
        return $result;
    }
    
    /**
     * @return string
     */
    public function getInventoryStoreListQuery($storeList = [])
    {
        $result = '';
        if (empty($storeList) == false) {
            $collection = $this->sourceInterface->create()->getCollection();
            $result = $collection
                ->addFieldToSelect('source_code')
                ->addFieldToSelect('name')
                ->addFieldToSelect('latitude')
                ->addFieldToSelect('longitude')
                ->addFieldToSelect('country_id')
                ->addFieldToSelect('region_id')
                ->addFieldToSelect('region')
                ->addFieldToSelect('city')
                ->addFieldToSelect('email')
                ->addFieldToSelect('phone')
                ->addFieldToFilter('source_code', ['in' => $storeList])
                ->getSelect()
                ->__toString();
        }
        return $result;
    }

    /**
     * Get Inventory Store collection
     * @return Magento\InventoryApi\Api\Data\SourceItemInterface
     */
    public function getInventoryStoreCollection()
    {
        $result = null;
        try {
            $collection = $this->sourceInterface->create()->getCollection();
            if ($collection->getSize()) {
                $result = $collection;
            }
        } catch (\Exception $e) {
            throw new StateException(
                __('Error : '. $e->getMessage())
            );
        }
        return $result;
    }
}
