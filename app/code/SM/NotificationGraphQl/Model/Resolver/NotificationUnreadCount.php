<?php
namespace SM\NotificationGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use SM\Notification\Api\CustomerMessageRepositoryInterface;
use Magento\Framework\Logger\Monolog;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;

/**
 * Class NotificationUnreadCount
 * @package SM\NotificationGraphQl\Model\Resolver
 */
class NotificationUnreadCount implements ResolverInterface
{
    /**
     * @var CustomerMessageRepositoryInterface
     */
    protected $customerMessageRepository;

    /**
     * @var Monolog
     */
    protected $logger;

    /**
     * NotificationUnreadCount constructor.
     * @param CustomerMessageRepositoryInterface $customerMessageRepository
     * @param Monolog $logger
     */
    public function __construct(
        CustomerMessageRepositoryInterface $customerMessageRepository,
        Monolog $logger
    ) {
        $this->customerMessageRepository = $customerMessageRepository;
        $this->logger = $logger;
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

        /** @var \SM\Notification\Model\ResourceModel\CustomerMessage\Collection $customerMessageCollection */
        $customerMessageCollection = $this->customerMessageRepository->getCollectionByIds($customerId);
        $customerMessageCollection->addFieldToFilter('is_read', ['neq' => 1]);

        return $customerMessageCollection->getSize();
    }
}
