<?php

declare(strict_types=1);

namespace SM\Reports\Model\ReportViewedProductSummary;

use SM\Reports\Api\Entity\ReportViewedProductSummaryInterface;

class Extractor
{
    /**
     * @param ReportViewedProductSummaryInterface[] $reportViewedProductSummaries
     * @return int[]
     */
    public function extractProductIds(array $reportViewedProductSummaries): array
    {
        $productIds = [];
        foreach ($reportViewedProductSummaries as $reportViewedProductSummary) {
            $productIds[] = $reportViewedProductSummary->getProductId();
        }

        return $productIds;
    }
}
