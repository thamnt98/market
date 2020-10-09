<?php

declare(strict_types=1);

namespace SM\Search\Model\Search\SearchResult;

use Magento\Framework\Api\Search\DocumentInterface;

class Extractor
{
    /**
     * @param DocumentInterface[] $documents
     * @return int[]
     */
    public function extractProductIds(array $documents): array
    {
        $productIds = [];
        foreach ($documents as $document) {
            $productIds[] = $document->getId();
        }

        return $productIds;
    }
}
