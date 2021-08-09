<?php

namespace SM\RecommendSearchCatalogGraphQl\Model\Resolver;

use Magento\Framework\App\Action\Context;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Search\Model\AutocompleteInterface;
use Magento\Search\Model\QueryFactory;
use SM\RecommendSearchCatalogGraphQl\Api\RecommendProductInterface;
use SM\Search\Api\Data\Response\SuggestionSearchResultInterface;
use SM\Search\Api\SearchInterface;
use SM\Search\Helper\Config;
use SM\Search\Model\Search\Suggestion\Product\Preparator as ProductPreparator;
use Magento\Search\Controller\Ajax\Suggest as BaseController;

/**
 * Class Suggest
 * @package SM\RecommendSearchCatalogGraphQl\Model\Resolver
 */
class Suggest extends BaseController implements ResolverInterface
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
     * @var RecommendProductInterface
     */
    protected $recommendProductRepository;

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
        ProductPreparator $productPreparator,
        RecommendProductInterface $recommendProductRepository
    )
    {
        parent::__construct($context, $autocomplete);
        $this->search = $search;
        $this->queryFactory = $queryFactory;
        $this->productPreparator = $productPreparator;
        $this->recommendProductRepository = $recommendProductRepository;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($args['keyword']) || empty($args['keyword'])) {
            return [
                SuggestionSearchResultInterface::TYPE => '',
                SuggestionSearchResultInterface::TYPO_SUGGEST_KEYWORD => $args['keyword'] ?? '',
                SuggestionSearchResultInterface::PRODUCTS => [],
                SuggestionSearchResultInterface::TOTAL_COUNT => 0,
            ];
        }

        try {
            $catId = $args['category_id'] ?? 0;
            $pageSize = $args['page_size'] ?? null;
            $currentPage = $args['current_page'] ?? null;
            $results = $this->search->suggestByKeyword($args['keyword'], $catId, $pageSize, $currentPage);
            $products = $this->productPreparator->prepareProducts($results->getProducts(), $catId == 0, $args['keyword']);
            if ($catId != 0) {
                $categoryName = $this->recommendProductRepository->getCategoryNameByCategoryId($catId);
                foreach ($products as $key => $product) {
                    $products[$key]['category_names'] = [$categoryName];
                }
            }
            $responseData = [
                SuggestionSearchResultInterface::TYPE => $results->getType(),
                SuggestionSearchResultInterface::TYPO_SUGGEST_KEYWORD => $args['keyword'],
                SuggestionSearchResultInterface::PRODUCTS => $results->getTotalCount() > 0
                    ? $products
                    : $this->getSearchQueryNoItem($args['keyword']),
                SuggestionSearchResultInterface::TOTAL_COUNT => $results->getTotalCount(),
            ];
        } catch (\Exception $exception) {
            $responseData = [
                Config::ERROR => $exception->getMessage()
            ];
        }
        return $responseData;
    }

    /**
     * @return array
     */
    protected function getSearchQueryNoItem($query): array
    {
        $items = [];
        $items[0][Config::PRODUCT_NAME_FIELD_NAME] = $query;
        $items[0][Config::PRODUCT_URL_FIELD_NAME] = '';
        $items[0][Config::CATEGORY_NAMES_ATTRIBUTE_CODE] = [];
        return $items;
    }
}
