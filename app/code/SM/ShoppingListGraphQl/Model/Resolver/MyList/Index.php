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
     * Id of current customer
     *
     * @var int|null
     */
    protected $_customerId = null;

    /**
     * Wishlist data
     *
     * @var \Magento\MultipleWishlist\Helper\Data
     */
    protected $_wishlistData = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\MultipleWishlist\Helper\Data $wishlistData
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param array $data
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
        $this->_customerId = $customerId;
        $items = $this->getWishlists()->getItems();
        $defaultId = $this->getDefaultWishlist()->getId();
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

    /**
     * @return \Magento\Wishlist\Model\ResourceModel\Wishlist\Collection
     */
    public function getWishlists()
    {
        return $this->_wishlistData->getCustomerWishlists($this->_getCustomerId());
    }

    /**
     * @return int|null
     */
    protected function _getCustomerId()
    {
        return $this->_customerId;
    }

    /**
     * Retrieve default wishlist for current customer
     *
     * @return \Magento\Wishlist\Model\Wishlist
     */
    public function getDefaultWishlist()
    {
        return $this->_wishlistData->getDefaultWishlist($this->_customerId);
    }
}
