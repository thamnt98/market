<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalogPrice
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   hadi <ashadi.sejati@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalogPrice\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Trans\IntegrationCatalogPrice\Api\PromotionPriceRepositoryInterface;
use Trans\IntegrationCatalogPrice\Api\Data\PromotionPriceInterface;
use Trans\IntegrationCatalogPrice\Api\Data\PromotionPriceInterfaceFactory;
use Trans\IntegrationCatalogPrice\Model\ResourceModel\PromotionPrice as ResourceModel;
use Trans\IntegrationCatalogPrice\Model\ResourceModel\PromotionPrice\Collection;
use Trans\IntegrationCatalogPrice\Model\ResourceModel\PromotionPrice\CollectionFactory;
use Magento\InventoryApi\Api\Data\SourceInterfaceFactory as SourceInterface;

/**
 *
 */
class PromotionPriceRepository implements PromotionPriceRepositoryInterface
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
     * @var PromotionPriceInterface
     */
    protected $interface;

    /**
     * @var SourceInterface
     */
    protected $sourceInterface;

    /**
     * @param CollectionFactory $collectionFactory
     * @param PromotionPriceInterface $promotionPriceInterface
     * @param ResourceModel $resource
     * @param PromotionPriceInterfaceFactory $interface
     * @param SourceInterface $sourceInterface
    */

    public function __construct(
        CollectionFactory $collectionFactory,
        PromotionPriceInterface $promotionPriceInterface,
        ResourceModel $resource,
        PromotionPriceInterfaceFactory $interface,
        SourceInterface $sourceInterface
    ) {
        $this->collectionFactory          = $collectionFactory;
        $this->promotionPriceInterface = $promotionPriceInterface;
        $this->resource                   = $resource;
        $this->interface                  = $interface;
        $this->sourceInterface            = $sourceInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($id)
    {
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
        $collection->addFieldToFilter(PromotionPriceInterface::SKU, $sku);

        $getLastCollection =null;
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
        $collection->addFieldToFilter(PromotionPriceInterface::SKU, $sku);
        $collection->addFieldToFilter(PromotionPriceInterface::SOURCE_CODE, $store);

        $getLastCollection =null;
        if ($collection->getSize()) {
            $getLastCollection = $collection->getFirstItem();
        }
        return $getLastCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function loadDataPromoFive($data)
    {
        if (empty($data['promotion_type'])) {
            throw new StateException(__(
                'Parameter promotion_type are empty !'
            ));
        }
        if (empty($data['discount_type'])) {
            throw new StateException(__(
                'Parameter discount_type are empty !'
            ));
        }
        if (empty($data['mix_and_match_code'])) {
            throw new StateException(__(
                'Parameter mix_and_match_code are empty !'
            ));
        }
        if (empty($data['item_type'])) {
            throw new StateException(__(
                'Parameter item_type are empty !'
            ));
        }
        if (empty($data['store_code'])) {
            throw new StateException(__(
                'Parameter store_code are empty !'
            ));
        }
        $collection = $this->interface->create()->getCollection();
        $collection->addFieldToFilter(PromotionPriceInterface::PIM_PROMOTION_TYPE, $data['promotion_type']);
        $collection->addFieldToFilter(PromotionPriceInterface::PIM_DISCOUNT_TYPE, $data['discount_type']);
        $collection->addFieldToFilter(PromotionPriceInterface::PIM_MIX_MATCH_CODE, $data['mix_and_match_code']);
        $collection->addFieldToFilter(PromotionPriceInterface::PIM_ITEM_TYPE, $data['item_type']);
        $collection->addFieldToFilter(PromotionPriceInterface::PIM_STORECODE, $data['store_code']);

        $getLastCollection =null;
        if ($collection->getSize()) {
            $getLastCollection = $collection->getFirstItem();
        }
        return $getLastCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function loadDataPromoFiveCheck($data)
    {
        if (empty($data['promotion_type'])) {
            throw new StateException(__(
                'Parameter promotion_type are empty !'
            ));
        }
        if (empty($data['discount_type'])) {
            throw new StateException(__(
                'Parameter discount_type are empty !'
            ));
        }
        if (empty($data['mix_and_match_code'])) {
            throw new StateException(__(
                'Parameter mix_and_match_code are empty !'
            ));
        }
        if (empty($data['sku'])) {
            throw new StateException(__(
                'Parameter sku are empty !'
            ));
        }
        $collection = $this->interface->create()->getCollection();
        $collection->addFieldToFilter(PromotionPriceInterface::PIM_PROMOTION_TYPE, $data['promotion_type']);
        $collection->addFieldToFilter(PromotionPriceInterface::PIM_DISCOUNT_TYPE, $data['discount_type']);
        $collection->addFieldToFilter(PromotionPriceInterface::PIM_MIX_MATCH_CODE, $data['mix_and_match_code']);
        $collection->addFieldToFilter(PromotionPriceInterface::PIM_SKU, $data['sku']);

        $getLastCollection =null;
        if ($collection->getSize()) {
            $getLastCollection = $collection->getFirstItem();
        }
        return $getLastCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function loadDataPromoByPromoId($data)
    {
        if (empty($data)) {
            throw new StateException(__(
                'Parameter promotion id are empty !'
            ));
        }
        $collection = $this->interface->create()->getCollection();
        $collection->addFieldToFilter(PromotionPriceInterface::PIM_PROMOTION_ID, $data);

        $getLastCollection =null;
        if ($collection->getSize()) {
            $getLastCollection = $collection->getFirstItem();
        }
        return $getLastCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function loadDataPromoByPromoIdStoreCode($promotionid, $storecode)
    {
        if (empty($promotionid)) {
            throw new StateException(__(
                'Parameter promotion id are empty !'
            ));
        }
        if (empty($storecode)) {
            throw new StateException(__(
                'Parameter store code are empty !'
            ));
        }
        $collection = $this->interface->create()->getCollection();
        $collection->addFieldToFilter(PromotionPriceInterface::PIM_PROMOTION_ID, $promotionid);
        $collection->addFieldToFilter(PromotionPriceInterface::PIM_STORECODE, $storecode);

        $getLastCollection = null;
        if ($collection->getSize()) {
            $getLastCollection = $collection->getFirstItem();
        }
        return $getLastCollection;
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
        $collection->addFieldToFilter(PromotionPriceInterface::STORE_ATTR_CODE, $code);
        $collection->addFieldToFilter(PromotionPriceInterface::SOURCE_CODE, $store);

        $getLastCollection =null;
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
        $collection->addFieldToFilter(PromotionPriceInterface::SOURCE_CODE, $store);

        $getLastCollection =null;
        if ($collection->getSize()) {
            $getLastCollection = $collection->getFirstItem();
        }
        return $getLastCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function save(PromotionPriceInterface $data)
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
    public function delete(PromotionPriceInterface $data)
    {
        /** @var StorePriceInterface|\Magento\Framework\Model\AbstractModel $data */
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
}
