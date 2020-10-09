<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Ilma Dinnia Alghani <ilma.dinnia@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalog\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Trans\IntegrationCatalog\Api\ProductAssociationRepositoryInterface;
use Trans\IntegrationCatalog\Api\Data\ProductAssociationInterface;
use Trans\IntegrationCatalog\Api\Data\ProductAssociationInterfaceFactory;
use Trans\IntegrationCatalog\Model\ResourceModel\ProductAssociation as ResourceModel;
use Trans\IntegrationCatalog\Model\ResourceModel\ProductAssociation\Collection;
use Trans\IntegrationCatalog\Model\ResourceModel\ProductAssociation\CollectionFactory;
use Magento\InventoryApi\Api\Data\SourceInterfaceFactory as SourceInterface;

/**
 *
 */
class ProductAssociationRepository implements ProductAssociationRepositoryInterface
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
     * @var ProductAssociationInterface
     */
    protected $interface;

    /**
     * @var SourceInterface
     */
    protected $sourceInterface;

    /**
     * @param CollectionFactory $collectionFactory
     * @param ProductAssociationInterface $ProductAssociationInterface
     * @param ResourceModel $resource
     * @param ProductAssociationInterfaceFactory $interface
     * @param SourceInterface $sourceInterface
    */

    public function __construct(
        CollectionFactory $collectionFactory,
        ProductAssociationInterface $ProductAssociationInterface,
        ResourceModel $resource,
        ProductAssociationInterfaceFactory $interface,
        SourceInterface $sourceInterface
    ) {
        $this->collectionFactory          = $collectionFactory;
        $this->ProductAssociationInterface = $ProductAssociationInterface;
        $this->resource                   = $resource;
        $this->interface                  = $interface;
        $this->sourceInterface            = $sourceInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function save(ProductAssociationInterface $dataIntegrationCatalog)
    {
        /** @var StorePriceInterface|\Magento\Framework\Model\AbstractModel $data */
        try {
            $this->resource->save($dataIntegrationCatalog);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the data: %1',
                $exception->getMessage()
            ));
        }
        return $dataIntegrationCatalog;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(ProductAssociationInterface $dataIntegrationCatalog)
    {
        /** @var StorePriceInterface|\Magento\Framework\Model\AbstractModel $dataIntegrationCatalog */
        $id = $dataIntegrationCatalog->getId();

        try {
            unset($this->instances[$id]);
            $this->resource->delete($dataIntegrationCatalog);
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
    public function loadDataPromoByPromoId($data)
    {
        if (empty($data)) {
            throw new StateException(__(
                'Parameter pim id are empty !'
            ));
        }
        $collection = $this->interface->create()->getCollection();
        $collection->addFieldToFilter(ProductAssociationInterface::PIM_ID, $data);

        $getLastCollection = null;
        if ($collection->getSize()) {
            $getLastCollection = $collection->getFirstItem();
        }
        return $getLastCollection;
    }
}
