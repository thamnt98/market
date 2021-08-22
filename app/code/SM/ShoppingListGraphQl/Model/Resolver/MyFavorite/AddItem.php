<?php

namespace SM\ShoppingListGraphQl\Model\Resolver\MyFavorite;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use SM\ShoppingList\Model\ShoppingListItemRepository;

/**
 * Class AddItem
 * @package SM\ShoppingListGraphQl\Model\Resolver\MyFavorite
 */
class AddItem implements ResolverInterface
{
    /**
     * Wishlist data
     *
     * @var \Magento\MultipleWishlist\Helper\Data
     */
    protected $wishlistData = null;

    /**
     * @var ShoppingListItemRepository
     */
    protected $shoppingListItemRepository;

    /**
     * AddItem constructor.
     * @param \Magento\MultipleWishlist\Helper\Data $wishlistData
     * @param ShoppingListItemRepository $shoppingListItemRepository
     */
    public function __construct(
        \Magento\MultipleWishlist\Helper\Data $wishlistData,
        ShoppingListItemRepository $shoppingListItemRepository
    )
    {
        $this->wishlistData = $wishlistData;
        $this->shoppingListItemRepository = $shoppingListItemRepository;
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
        if (!isset($args['product_id'])) {
            throw new GraphQlInputException(__('"product id" value should be specified'));
        }
        $productId = $args['product_id'];
        $shoppingListIds = [$this->wishlistData->getDefaultWishlist($customerId)->getId()];
        $result = $this->shoppingListItemRepository->add($shoppingListIds, $productId);
        $data = [
            'status' => $result->getStatus(),
            'message' => __("Product has been added to My Favorites.")
        ];
        if (!$result->getStatus()) {
            $data['message'] = $result->getMessage();
        }
        return $data;
    }
}
