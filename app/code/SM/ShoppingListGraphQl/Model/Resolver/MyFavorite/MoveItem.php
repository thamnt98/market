<?php

namespace SM\ShoppingListGraphQl\Model\Resolver\MyFavorite;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Webapi\Exception as WebapiException;
use SM\ShoppingList\Api\Data\ResultDataInterface;
use SM\ShoppingList\Model\ShoppingListItemRepository;

/**
 * Class MoveItem
 * @package SM\ShoppingListGraphQl\Model\Resolver\MyFavorite
 */
class MoveItem implements ResolverInterface
{

    /**
     * @var ShoppingListItemRepository
     */
    protected $shoppingListItemRepository;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\MultipleWishlist\Helper\Data $wishlistData
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param array $data
     */
    public function __construct(
        ShoppingListItemRepository $shoppingListItemRepository
    )
    {
        $this->shoppingListItemRepository = $shoppingListItemRepository;
    }

    /**
     * @param Field $field
     * @param ContextInterface $context
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

        if (!isset($args['item_id'])) {
            throw new GraphQlInputException(__('"item id" value should be specified'));
        }
        if (!isset($args['wish_list_ids'])) {
            throw new GraphQlInputException(__('"wish list id" array should be specified'));
        }
        $itemId = $args["item_id"];
        $selected = $args["wish_list_ids"];
        try {
            /** @var ResultDataInterface $result */
            $result = $this->shoppingListItemRepository->move($itemId, $selected);
            $data['status'] = $result->getStatus();
            if ($result->getStatus()) {
                $data['message'] = 'This item has been moved to ';
                $wishlistNames = [];
                foreach ($result->getResult() as $item) {
                    $wishlistNames[] = $item->getName();
                }
                $data['message'] .= implode(", ", $wishlistNames);
            } else {
                $data['message'] = $result->getMessage();
            }
        } catch (WebapiException $e) {
            $data = [
                'status' => 0,
                'message' => __("Item with '%1' does not exist", $itemId)
            ];
        }
        return $data;
    }
}
