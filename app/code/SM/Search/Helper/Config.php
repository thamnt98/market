<?php

declare(strict_types=1);

namespace SM\Search\Helper;

use Magento\CatalogSearch\Model\Autocomplete\DataProvider;
use Magento\Framework\App\Helper\AbstractHelper;

class Config extends AbstractHelper
{
    const XML_PATH_GLOBAL_SEARCH_DEBUG = 'catalog_search/global_search/debug';
    const XML_PATH_GLOBAL_SEARCH_SEARCH_BY_ID = 'catalog_search/global_search/search_by_id';
    const XML_PATH_LATEST_SEARCH_SIZE = 'catalog_search/latest_search/size';
    const XML_PATH_POPULAR_SEARCH_SIZE = 'catalog_search/popular_search/size';
    const XML_PATH_SUGGESTION_MAIN_CATEGORY_LIMIT = 'catalog_search/suggestion/main_category_limit';
    const XML_PATH_SUGGESTION_PRODUCT_SEARCH_NO_RESULT = 'catalog_search/search_no_result_page/suggest_category';

    const CATEGORY_NAMES_ATTRIBUTE_CODE = 'category_names';
    const CATEGORY_IDS_ATTRIBUTE_CODE = 'category_ids';
    const CUSTOMER_ID_ATTRIBUTE_CODE = 'customer_id';
    const STORE_ID_ATTRIBUTE_CODE = 'store_id';

    const QUICK_SEARCH_CONTAINER = 'quick_search_container';
    const SEARCH_PARAM_SEARCH_TEXT_FIELD_NAME = 'search_term';
    const SEARCH_PARAM_CATEGORY_FIELD_NAME = 'cat';

    const SEARCH_COUNT_FIELD_NAME = 'popularity';
    const SEARCH_NUM_RESULTS_FIELD_NAME = 'num_results';

    const PRODUCT_NAME_FIELD_NAME = 'name';
    const PRODUCT_URL_FIELD_NAME = 'url';
    const IS_QUICK_SUGGESTION_REQUEST = 'is_quick_suggestion_request';
    const SUGGESTION_KEYWORD = 'suggestion_keyword';

    const PRODUCT_ATTRIBUTE_BARCODE = 'barcode';
    const PRODUCT_ATTRIBUTE_SKU = 'sku';

    const ERROR = 'error';

    /**
     * @return bool
     */
    public function isEnableDebug(): bool
    {
        return (bool) $this->scopeConfig->getValue(
            self::XML_PATH_GLOBAL_SEARCH_DEBUG
        );
    }

    /**
     * @return bool
     */
    public function isEnableSearchById(): bool
    {
        return (bool) $this->scopeConfig->getValue(
            self::XML_PATH_GLOBAL_SEARCH_SEARCH_BY_ID
        );
    }

    /**
     * @return int
     */
    public function getLatestSearchSize(): int
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_LATEST_SEARCH_SIZE
        );
    }

    /**
     * @return int
     */
    public function getPopularSearchSize(): int
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_POPULAR_SEARCH_SIZE
        );
    }

    /**
     * @return int
     */
    public function getSearchAutocompleteLimit(): int
    {
        return (int) $this->scopeConfig->getValue(
            DataProvider::CONFIG_AUTOCOMPLETE_LIMIT
        );
    }

    /**
     * @return int
     */
    public function getSuggestionMainCategoryLimit(): int
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_SUGGESTION_MAIN_CATEGORY_LIMIT
        );
    }

    /**
     * Using for get product in search no result page
     * @return int
     */
    public function getSuggestCategoryIdOnNoResult(): int
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_SUGGESTION_PRODUCT_SEARCH_NO_RESULT
        );
    }
}
