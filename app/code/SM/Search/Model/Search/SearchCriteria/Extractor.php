<?php

declare(strict_types=1);

namespace SM\Search\Model\Search\SearchCriteria;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use SM\Search\Helper\Config;

class Extractor
{
    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return string
     */
    public function extractSearchTextParam(SearchCriteriaInterface $searchCriteria): string
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() == Config::SEARCH_PARAM_SEARCH_TEXT_FIELD_NAME) {
                    return $filter->getValue();
                }
            }
        }

        return '';
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return string
     */
    public function extractCategoryParam(SearchCriteriaInterface $searchCriteria): string
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() == Config::CATEGORY_IDS_ATTRIBUTE_CODE) {
                    return (string) $filter->getValue();
                }
            }
        }

        return '';
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return string
     */
    public function extractVisibilityParam(SearchCriteriaInterface $searchCriteria): string
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() == ProductInterface::VISIBILITY) {
                    return (string) $filter->getValue();
                }
            }
        }

        return '';
    }
}
