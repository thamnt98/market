<?php

declare(strict_types=1);

namespace SM\Reports\Model\Entity;

use Magento\Framework\Model\AbstractModel;
use SM\Reports\Api\Entity\ReportViewedProductSummaryInterface;
use SM\Reports\Model\ResourceModel\ReportViewedProductSummary as ResourceModel;

class ReportViewedProductSummary extends AbstractModel implements ReportViewedProductSummaryInterface
{
    /**
     * Construct method
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductId(int $productId): ReportViewedProductSummaryInterface
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductId(): int
    {
        return (int) $this->getData(self::PRODUCT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setPopularity(int $popularity): ReportViewedProductSummaryInterface
    {
        return $this->setData(self::POPULARITY, $popularity);
    }

    /**
     * {@inheritdoc}
     */
    public function getPopularity(): int
    {
        return (int) $this->getData(self::POPULARITY);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId(int $storeId): ReportViewedProductSummaryInterface
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreId(): int
    {
        return (int) $this->getData(self::STORE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerId(int $customerId): ReportViewedProductSummaryInterface
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerId(): int
    {
        return (int) $this->getData(self::CUSTOMER_ID);
    }
}
