<?php

namespace SM\ShoppingListGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use SM\ShoppingList\Model\ShoppingListItemRepository;

/**
 * Class RemoveItem
 * @package SM\ShoppingListGraphQl\Model\Resolver
 */
class RemoveItem implements ResolverInterface
{
    /**
     * @var ShoppingListItemRepository
     */
    protected $shoppingListItemRepository;

    /**
     * RemoveItem constructor.
     * @param ShoppingListItemRepository $shoppingListItemRepository
     */
    public function __construct(
        ShoppingListItemRepository $shoppingListItemRepository
    )
    {
        $this->shoppingListItemRepository = $shoppingListItemRepository;
    }

    /**
     * @param Field $field
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $customerId = $context->getUserId();
        /* Guest checking */
        if (!$customerId && 0 === $customerId) {
            throw new GraphQlAuthorizationException(__('The current user cannot perform operations on wishlist'));
        }

        if (!isset($args['item_id'])) {
            throw new GraphQlInputException(__('"item id" value should be specified'));
        }
        $itemId = $args['item_id'];
        $data['status'] = 0;
        try {
            if ($this->shoppingListItemRepository->deleteById($itemId)) {
                $data = [
                    'status' => 1,
                    'message' => 'This item has been already removed'
                ];
            } else {
                $data['message'] = 'This item is not found';
            }
        } catch (Exception $e) {
            $data['message'] = $e->getMessage();
        }
        return $data;
    }
}
