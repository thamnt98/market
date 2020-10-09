<?php

declare(strict_types=1);

namespace SM\Reports\Plugin\SM\Product\Api\Repository;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Reports\Model\Event;
use Magento\Reports\Model\ReportStatus;
use Psr\Log\LoggerInterface;
use SM\Product\Api\Repository\ProductRepositoryInterface as BaseProductRepositoryInterface;
use SM\Reports\Model\ReportEvent\Creator as ReportEventCreator;
use SM\Reports\Model\ReportViewedProduct\Creator as ReportViewedProductCreator;

class ProductRepositoryInterface
{
    /**
     * @var ReportStatus
     */
    protected $reportStatus;

    /**
     * @var ReportViewedProductCreator
     */
    protected $reportViewedProductCreator;

    /**
     * @var ReportEventCreator
     */
    protected $reportEventCreator;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * ProductRepositoryInterface constructor.
     * @param ReportStatus $reportStatus
     * @param ReportViewedProductCreator $reportViewedProductCreator
     * @param ReportEventCreator $reportEventCreator
     * @param LoggerInterface $logger
     */
    public function __construct(
        ReportStatus $reportStatus,
        ReportViewedProductCreator $reportViewedProductCreator,
        ReportEventCreator $reportEventCreator,
        LoggerInterface $logger
    ) {
        $this->reportStatus = $reportStatus;
        $this->reportViewedProductCreator = $reportViewedProductCreator;
        $this->reportEventCreator = $reportEventCreator;
        $this->logger = $logger;
    }

    /**
     * @param BaseProductRepositoryInterface $subject
     * @param ProductInterface $result
     * @param int $customerId
     * @param string $sku
     * @param bool $editMode
     * @param int|null $storeId
     * @param bool $forceReload
     * @return ProductInterface
     */
    public function afterGet(
        BaseProductRepositoryInterface $subject,
        ProductInterface $result,
        int $customerId,
        string $sku,
        bool $editMode = false,
        ?int $storeId = null,
        bool $forceReload = false
    ): ProductInterface {
        try {
            if (!$this->reportStatus->isReportEnabled((string) Event::EVENT_PRODUCT_VIEW)) {
                return $result;
            }

            $productId = (int) $result->getId();

            $this->reportViewedProductCreator->create($productId, $customerId);
            $this->reportEventCreator->create(Event::EVENT_PRODUCT_VIEW, $productId, $customerId);
        } catch (\Exception $exception) {
            $this->logger->error('Could not increase report viewed product', [
                'exception_code' => $exception->getCode(),
                'exception_message' => $exception->getMessage(),
                'exception_trace' => $exception->getTraceAsString(),
            ]);
        }

        return $result;
    }
}
