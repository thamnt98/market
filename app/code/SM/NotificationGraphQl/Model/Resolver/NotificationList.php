<?php
namespace SM\NotificationGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use SM\Notification\Api\CustomerMessageRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;

/**
 * Class NotificationList
 * @package SM\NotificationGraphQl\Model\Resolver
 */
class NotificationList implements ResolverInterface
{
    /**
     * @var CustomerMessageRepositoryInterface
     */
    protected $customerMessageRepository;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    protected $searchCriteriaBuilderFactory;

    /**
     * @var SortOrderBuilder
     */
    protected $sortOrderBuilder;

    /**
     * NotificationList constructor.
     * @param CustomerMessageRepositoryInterface $customerMessageRepository
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param SortOrderBuilder $sortOrderBuilder
     */
    public function __construct(
        CustomerMessageRepositoryInterface $customerMessageRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        SortOrderBuilder $sortOrderBuilder
    ) {
        $this->customerMessageRepository = $customerMessageRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $customerId = $context->getUserId();

        if (!$customerId || $customerId == 0) {
            throw new GraphQlAuthorizationException(__('Notification is only available for logged in customer'));
        }

        $pageSize = $args['pageSize'] ?? 10;
        $currentPage = $args['currentPage'] ?? 1;
        $type = $args['type'] ?? false;

        $sortOrder = $this->sortOrderBuilder
            ->setField('created_at')
            ->setDescendingDirection()
            ->create();

        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteriaBuilder->setPageSize($pageSize)
            ->setCurrentPage($currentPage)
            ->addSortOrder($sortOrder);

        if ($type) {
            $searchCriteriaBuilder->addFilter('event', $type);
        }

        $result = $this->customerMessageRepository->getList($customerId, $searchCriteriaBuilder->create());

        $data = [
            'total_count' => $result->getTotalCount(),
            'items' => []
        ];

        foreach ($result->getItems() as $item) {
            $itemData = [
                "message_id" => $item->getMessageId(),
                "title" => $item->getTitle(),
                "content" => $item->getContent(),
                "is_read" => $item->getIsRead(),
                "event" => $item->getEvent(),
                "event_label" => $item->getEventLabel(),
                "image" => $item->getImage(),
                "redirect_type" => $item->getRedirectType(),
                "redirect_id" => $item->getRedirectId(),
                "created_at" => $item->getCreatedAt(),
                "start_date" => $item->getStartDate(),
                "end_date" => $item->getEndDate(),
                "highlight_title" => $item->getHighlightTitle(),
                "highlight_content" => $item->getHighlightContent(),
                "model" => $item
            ];

            $data['items'][] = $itemData;
        }

        return $data;
    }
}
