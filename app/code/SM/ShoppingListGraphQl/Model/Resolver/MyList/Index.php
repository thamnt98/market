<?php

namespace SM\ShoppingListGraphQl\Model\Resolver\MyList;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class Index
 * @package SM\ShoppingListGraphQl\Model\Resolver\MyList
 */
class Index implements ResolverInterface
{
    /**
     * Wishlist data
     *
     * @var \Magento\MultipleWishlist\Helper\Data
     */
    protected $_wishlistData = null;

    /**
     * @param \Magento\MultipleWishlist\Helper\Data $wishlistData
     */
    public function __construct(
        \Magento\MultipleWishlist\Helper\Data $wishlistData
    )
    {
        $this->_wishlistData = $wishlistData;
    }

    /**
     * @param Field $field
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     * @throws GraphQlAuthorizationException
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $customerId = $context->getUserId();

        /* Guest checking */
        if (!$customerId && 0 === $customerId) {
            throw new GraphQlAuthorizationException(__('The current user cannot perform operations on wishlist'));
        }
        $items = $this->_wishlistData->getCustomerWishlists($customerId)->getItems();
        $defaultId = $this->_wishlistData->getDefaultWishlist($customerId)->getId();
        if (isset($items[$defaultId])) {
            unset($items[$defaultId]);
        }
        $myList['total_records'] = count($items);
        $myList['items'] = [];
        foreach ($items as $key => $item) {
            $products = $item->getItemCollection()->getItems();
            $item = $item->toArray();
            $item['total_product'] = count($products);
            $item['products'] = [];
            foreach ($products as $product) {
                $item['products'][] = [
                    'id' => $product->getId(),
                    'qty' => $product->getData('qty'),
                    'description' => $product->getDescription(),
                    'added_at' => $product->getAddedAt(),
                    'model' => $product,
                ];
            }
            $myList['items'][$key] = $item;
        }
        return $myList;
    }
}
