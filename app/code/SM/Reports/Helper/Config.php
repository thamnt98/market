<?php

declare(strict_types=1);

namespace SM\Reports\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Config extends AbstractHelper
{
    const XML_PATH_PRODUCT_RECOMMENDATION_SIZE = 'trans_reports/product/recommendation_size';

    const SEARCH_PARAM_RECOMMENDATION_LIMIT_FIELD_NAME = 'limit';

    /**
     * @return int
     */
    public function getRecommendationProductsSize(): int
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_PRODUCT_RECOMMENDATION_SIZE
        );
    }
}
