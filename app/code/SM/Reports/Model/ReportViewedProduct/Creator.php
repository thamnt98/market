<?php

declare(strict_types=1);

namespace SM\Reports\Model\ReportViewedProduct;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Reports\Model\Product\Index\ViewedFactory as ReportViewedProductFactory;
use Magento\Reports\Model\ResourceModel\Product\Index\Viewed as ResourceModel;
use Magento\Store\Model\StoreManagerInterface;

class Creator
{
    /**
     * @var ReportViewedProductFactory
     */
    protected $reportViewedProductFactory;

    /**
     * @var ResourceModel
     */
    protected $resourceModel;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Creator constructor.
     * @param ReportViewedProductFactory $reportViewedProductFactory
     * @param ResourceModel $resourceModel
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ReportViewedProductFactory $reportViewedProductFactory,
        ResourceModel $resourceModel,
        StoreManagerInterface $storeManager
    ) {
        $this->reportViewedProductFactory = $reportViewedProductFactory;
        $this->resourceModel = $resourceModel;
        $this->storeManager = $storeManager;
    }

    /**
     * @param int $productId
     * @param int $customerId
     * @throws NoSuchEntityException
     */
    public function create(int $productId, int $customerId): void
    {
        $entity = $this->reportViewedProductFactory->create();
        $entity->setProductId($productId);
        $entity->setStoreId($this->storeManager->getStore()->getId());
        $entity->setCustomerId($customerId);

        $this->resourceModel->save($entity);
    }
}
