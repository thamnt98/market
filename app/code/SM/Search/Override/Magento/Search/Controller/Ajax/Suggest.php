<?php

declare(strict_types=1);

namespace SM\Search\Override\Magento\Search\Controller\Ajax;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Json as ResultJson;
use Magento\Framework\Controller\Result\Redirect as RedirectResult;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Search\Controller\Ajax\Suggest as BaseController;
use Magento\Search\Model\AutocompleteInterface;
use Magento\Search\Model\QueryFactory;
use Magento\Search\Model\QueryInterface;
use SM\Search\Api\Data\Response\SuggestionSearchResultInterface;
use SM\Search\Api\SearchInterface;
use SM\Search\Helper\Config;
use SM\Search\Model\Search\Suggestion\Product\Preparator as ProductPreparator;

class Suggest extends BaseController
{
    /**
     * @var SearchInterface
     */
    protected $search;

    /**
     * @var QueryFactory
     */
    protected $queryFactory;

    /**
     * @var ProductPreparator
     */
    protected $productPreparator;

    /**
     * Suggest constructor.
     * @param Context $context
     * @param AutocompleteInterface $autocomplete
     * @param SearchInterface $search
     * @param QueryFactory $queryFactory
     * @param ProductPreparator $productPreparator
     */
    public function __construct(
        Context $context,
        AutocompleteInterface $autocomplete,
        SearchInterface $search,
        QueryFactory $queryFactory,
        ProductPreparator $productPreparator
    ) {
        parent::__construct($context, $autocomplete);
        $this->search = $search;
        $this->queryFactory = $queryFactory;
        $this->productPreparator = $productPreparator;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        if (!$this->getRequest()->getParam('q', false)) {
            /** @var RedirectResult $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_url->getBaseUrl());
            return $resultRedirect;
        }

        try {
            $query = $this->queryFactory->get();
            $catId = (int) $this->getRequest()->getParam('cat');
            $results = $this->search->suggestByKeyword($query->getQueryText(), $catId);
            $responseData = [
                SuggestionSearchResultInterface::TYPE => $results->getType(),
                SuggestionSearchResultInterface::TYPO_SUGGEST_KEYWORD => $results->getTypoSuggestKeyword(),
                SuggestionSearchResultInterface::PRODUCTS => $results->getTotalCount() > 0
                    ? $this->productPreparator->prepareProducts($results->getProducts(), $catId == 0)
                    : $this->getSearchQueryItem($query),
                SuggestionSearchResultInterface::TOTAL_COUNT => $results->getTotalCount(),
            ];
        } catch (\Exception $exception) {
            $responseData = [
                Config::ERROR => $exception->getMessage()
            ];
        }

        /** @var ResultJson $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($responseData);

        return $resultJson;
    }

    /**
     * @param QueryInterface $query
     * @return array
     */
    protected function getSearchQueryItem(QueryInterface $query): array
    {
        $items = [];

        $items[0][Config::PRODUCT_NAME_FIELD_NAME] = $query->getQueryText();
        $items[0][Config::PRODUCT_URL_FIELD_NAME] = '';
        $items[0][Config::CATEGORY_NAMES_ATTRIBUTE_CODE] = '';

        return $items;
    }
}
