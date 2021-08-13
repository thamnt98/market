<?php
namespace SM\NotificationGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use SM\Notification\Helper\Data;

/**
 * Class NotificationTypes
 * @package SM\NotificationGraphQl\Model\Resolver
 */
class NotificationTypes implements ResolverInterface
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * GetNotificationTypes constructor.
     * @param Data $helper
     */
    public function __construct(
        Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $result = [];
        $list = $this->helper->getEventConfig();

        foreach ($list as $item) {
            if ($item['enable']) {
                $result[] = [
                    'name' => $item['name'],
                    'value' => $item['event_type']
                ];
            }
        }

        return $result;
    }
}
