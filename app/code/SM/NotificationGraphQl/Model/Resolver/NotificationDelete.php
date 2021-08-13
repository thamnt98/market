<?php
namespace SM\NotificationGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use SM\Notification\Api\CustomerMessageRepositoryInterface;
use SM\Notification\Model\ResourceModel\CustomerMessage;
use Magento\Framework\Logger\Monolog;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;

/**
 * Class NotificationDelete
 * @package SM\NotificationGraphQl\Model\Resolver
 */
class NotificationDelete implements ResolverInterface
{
    /**
     * @var CustomerMessageRepositoryInterface
     */
    protected $customerMessageRepository;

    /**
     * @var CustomerMessage
     */
    protected $customerMessageResource;

    /**
     * @var Monolog
     */
    protected $logger;

    /**
     * NotificationDelete constructor.
     * @param CustomerMessageRepositoryInterface $customerMessageRepository
     * @param CustomerMessage $customerMessageResource
     * @param Monolog $logger
     */
    public function __construct(
        CustomerMessageRepositoryInterface $customerMessageRepository,
        CustomerMessage $customerMessageResource,
        Monolog $logger
    ) {
        $this->customerMessageRepository = $customerMessageRepository;
        $this->customerMessageResource = $customerMessageResource;
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

        if (empty($args['message_ids'])) {
            throw new GraphQlInputException(__('No message id specified'));
        }

        $messageIds = $args['message_ids'];

        /** @var \SM\Notification\Model\ResourceModel\CustomerMessage\Collection $customerMessageCollection */
        $customerMessageCollection = $this->customerMessageRepository->getCollectionByIds($customerId, $messageIds);

        $successCount = 0;
        $failureCount = 0;
        if ($customerMessageCollection->getSize()) {
            foreach ($customerMessageCollection->getItems() as $message) {
                try {
                    $this->customerMessageResource->delete($message);
                    $successCount++;
                } catch (\Exception $e) {
                    $this->logger->error('Error when trying to delete message with ID ' . $message->getId(), [$e->getMessage()]);
                    $failureCount++;
                }
            }
        }

        return [
            'success_count' => $successCount,
            'failure_count' => $failureCount
        ];
    }
}
