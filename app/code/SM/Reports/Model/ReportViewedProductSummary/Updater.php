<?php

declare(strict_types=1);

namespace SM\Reports\Model\ReportViewedProductSummary;

use Magento\Framework\Exception\LocalizedException;
use Magento\Reports\Model\Product\Index\Viewed;
use SM\Reports\Api\Entity\ReportViewedProductSummaryInterface;
use SM\Reports\Api\Repository\ReportViewedProductSummaryRepositoryInterface;

class Updater
{
    /**
     * @var ReportViewedProductSummaryRepositoryInterface
     */
    protected $repository;

    /**
     * @var bool
     */
    protected $isSaved = false;

    /**
     * Updater constructor.
     * @param ReportViewedProductSummaryRepositoryInterface $repository
     */
    public function __construct(
        ReportViewedProductSummaryRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * @param Viewed $viewed
     * @throws LocalizedException
     */
    public function update(Viewed $viewed): void
    {
        if (!$viewed->getCustomerId()) {
            return;
        }

        if ($this->isSaved) {
            return;
        }

        $entityData = [
            ReportViewedProductSummaryInterface::PRODUCT_ID => $viewed->getProductId(),
            ReportViewedProductSummaryInterface::POPULARITY => 1,
            ReportViewedProductSummaryInterface::CUSTOMER_ID => $viewed->getCustomerId(),
            ReportViewedProductSummaryInterface::STORE_ID => $viewed->getStoreId(),
        ];

        $this->repository->saveEntity($entityData);

        $this->isSaved = true;
    }
}
