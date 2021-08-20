<?php

namespace SM\ShoppingList\Block\Customer;

use Magento\Wishlist\Model\WishlistFactory as ShoppingListFactory;

class Wishlist extends \Magento\Wishlist\Block\Customer\Wishlist
{
    /**
     * @var \Magento\MultipleWishlist\Helper\Data
     */
    protected $wishlistData;

    /**
     * @var ShoppingListFactory
     */
    protected $wishlistFactory;

    public function __construct(
        \Magento\MultipleWishlist\Helper\Data $wishlistData,
        ShoppingListFactory $shoppingListFactory,
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Catalog\Helper\Product\ConfigurationPool $helperPool,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        array $data = []
    ) {
        parent::__construct($context, $httpContext, $helperPool, $currentCustomer, $postDataHelper, $data);
        $this->wishlistData = $wishlistData;
        $this->wishlistFactory = $shoppingListFactory;
    }

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAllItems()
    {
        $items = [];
        $customerId = $this->currentCustomer->getCustomerId();
        $wishlist = $this->getWishlistData($customerId);

        if ($wishlist) {
            foreach ($wishlist->getItemCollection() as $item) {
                $items[] = $item;
            }
        }

        return $items;
    }

    public function getWishlistData($customerId)
    {
        $data = $this->getRequest()->getParams();
        if (isset($data['wishlist_id'])) {
            $wishlist = $this->wishlistFactory->create()->load($data['wishlist_id']);
        } else {
            $wishlist = $this->wishlistData->getDefaultWishlist($customerId);
        }
        return $wishlist;
    }

}
