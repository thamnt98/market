<?php

namespace SM\ShoppingListGraphQl\Model\Resolver\MyList;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Webapi\Exception as WebapiException;
use SM\ShoppingList\Api\Data\ShoppingListDataInterface;
use SM\ShoppingList\Model\ShoppingListItemRepository;
use SM\ShoppingList\Model\ShoppingListRepository;

/**
 * Class Delete
 * @package SM\ShoppingListGraphQl\Model\Resolver\MyList
 */
class Delete implements ResolverInterface
{
    /**
     * @var ShoppingListRepository
     */
    protected $shoppingListRepository;

    /**
     * Create constructor.
     * @param ShoppingListItemRepository $shoppingListItemRepository
     */
    public function __construct(
        ShoppingListRepository $shoppingListRepository
    )
    {
        $this->shoppingListRepository = $shoppingListRepository;
    }

    /**
     * @param Field $field
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     * @throws GraphQlAuthorizationException
     * @throws GraphQlInputException
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $customerId = $context->getUserId();
        /* Guest checking */
        if (!$customerId && 0 === $customerId) {
            throw new GraphQlAuthorizationException(__('The current user cannot perform operations on wishlist'));
        }
        if (!isset($args['my_list_id'])) {
            throw new GraphQlInputException(__('"my list id" value should be specified'));
        }
        $myListId = $args['my_list_id'];
        /** @var ShoppingListDataInterface $shoppingList */
        try {
            $shoppingList = $this->shoppingListRepository->getById($myListId);
            $this->shoppingListRepository->delete($shoppingList->getWishlistId());
            return [
                'status' => 1,
                'message' => __("%1 has been successfully deleted.", $shoppingList->getName())
            ];
        } catch (WebapiException $e) {
            return [
                'status' => 0,
                'message' => $e->getMessage()
            ];
        }
    }
}
