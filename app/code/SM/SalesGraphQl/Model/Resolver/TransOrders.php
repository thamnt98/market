<?php
namespace SM\SalesGraphQl\Model\Resolver;

use Magento\Framework\Api\SearchCriteriaBuilder;
use SM\Sales\Model\ParentOrderRepository;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;

/**
 * Class TransOrders
 * @package SM\SalesGraphQl\Model\Resolver
 */
class TransOrders implements ResolverInterface
{
    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var ParentOrderRepository
     */
    protected $parentOrderRepository;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var FilterGroupBuilder
     */
    protected $filterGroupBuilder;

    /**
     * @var FilterGroupBuilder
     */
    protected $sortOrderBuilder;

    /**
     * TransOrders constructor.
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ParentOrderRepository $parentOrderRepository
     * @param FilterBuilder $filterBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ParentOrderRepository $parentOrderRepository,
        FilterBuilder $filterBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        SortOrderBuilder $sortOrderBuilder
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->parentOrderRepository = $parentOrderRepository;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $customerId = $context->getUserId();
        $storeId = $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();

        if (!$customerId) {
            throw new GraphQlAuthorizationException(
                __('Order history is only available to logged in user.')
            );
        }

        $currentPage = 1;
        $pageSize = 10;
        if (isset($args['currentPage'])) {
            $currentPage = $args['currentPage'];
        }
        if (isset($args['pageSize'])) {
            $pageSize = $args['pageSize'];
        }

        $filterGroups = [];
        if (isset($args['filter'])) {
            $filters = [];
            foreach ($args['filter'] as $field => $value) {
                $filters[] = $this->filterBuilder->setField($field)
                    ->setValue($value)
                    ->create();
            }

            $this->filterGroupBuilder->setFilters($filters);
            $filterGroups[] = $this->filterGroupBuilder->create();
        }

        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchCriteria->setFilterGroups($filterGroups);

        $searchCriteria->setCurrentPage($currentPage);
        $searchCriteria->setPageSize($pageSize);

        $sortOrder = $this->sortOrderBuilder
            ->setField(ParentOrderRepository::SORT_LATEST)
            ->setDirection("desc")
            ->create();
        $searchCriteria->setSortOrders([$sortOrder]);

        $results = $this->parentOrderRepository->getList($searchCriteria, $customerId);

        $maxPages = (int)ceil($results->getTotalCount() / $pageSize);

        return [
            'total_count' => $results->getTotalCount(),
            'items' => $results->getItems(),
            'page_info' => [
                'page_size' => $pageSize,
                'current_page' => $currentPage,
                'total_pages' => $maxPages,
            ]
        ];
    }
}
