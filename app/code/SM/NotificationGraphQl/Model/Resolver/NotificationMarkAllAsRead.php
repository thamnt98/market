<?php
namespace SM\NotificationGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use SM\Notification\Api\CustomerMessageRepositoryInterface;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;

/**
 * Class NotificationMarkAllAsRead
 * @package SM\NotificationGraphQl\Model\Resolver
 */
class NotificationMarkAllAsRead implements ResolverInterface
{
    /**
     * @var CustomerMessageRepositoryInterface
     */
    protected $customerMessageRepository;

    /**
     * NotificationMarkAsRead constructor.
     * @param CustomerMessageRepositoryInterface $customerMessageRepository
     */
    public function __construct(
        CustomerMessageRepositoryInterface $customerMessageRepository
    ) {
        $this->customerMessageRepository = $customerMessageRepository;
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

        $resultCount = $this->customerMessageRepository->updateReadAll($customerId, 'read');

        return [
            'success_count' => $resultCount,
            'failure_count' => 0
        ];
    }
}
