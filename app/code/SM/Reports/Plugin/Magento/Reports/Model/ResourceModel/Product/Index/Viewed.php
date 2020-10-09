<?php

declare(strict_types=1);

namespace SM\Reports\Plugin\Magento\Reports\Model\ResourceModel\Product\Index;

use Magento\Framework\Exception\LocalizedException;
use Magento\Reports\Model\Product\Index\Viewed as BaseModel;
use Magento\Reports\Model\ResourceModel\Product\Index\Viewed as BaseResourceModel;
use Psr\Log\LoggerInterface;
use SM\Reports\Model\ReportViewedProductSummary\Updater as ReportViewedProductSummaryUpdater;

class Viewed
{
    /**
     * @var ReportViewedProductSummaryUpdater
     */
    protected $reportViewedProductSummaryUpdater;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Query constructor.
     * @param ReportViewedProductSummaryUpdater $reportViewedProductSummaryUpdater
     * @param LoggerInterface $logger
     */
    public function __construct(
        ReportViewedProductSummaryUpdater $reportViewedProductSummaryUpdater,
        LoggerInterface $logger
    ) {
        $this->reportViewedProductSummaryUpdater = $reportViewedProductSummaryUpdater;
        $this->logger = $logger;
    }

    /**
     * @param BaseResourceModel $subject
     * @param BaseResourceModel $result
     * @param BaseModel $viewed
     * @return BaseResourceModel
     */
    public function afterSave(BaseResourceModel $subject, BaseResourceModel $result, BaseModel $viewed): BaseResourceModel
    {
        try {
            // save popularity
            $this->reportViewedProductSummaryUpdater->update($viewed);
        } catch (LocalizedException $exception) {
            $this->logger->critical('Error on save report_viewed_product_summary', [
                'exception_code' => $exception->getCode(),
                'exception_message' => $exception->getMessage(),
                'exception_trace' => $exception->getTraceAsString(),
            ]);
        }

        return $result;
    }
}
