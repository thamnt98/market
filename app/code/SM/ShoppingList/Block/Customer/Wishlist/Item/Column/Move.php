<?php
/**
 * SM\ShoppingList\Block\Customer\Wishlist\Item\Column
 *
 * @copyright Copyright © 2021 SmartOSC. All rights reserved.
 * @author    hungnv6@smartosc.com
 */

namespace SM\ShoppingList\Block\Customer\Wishlist\Item\Column;

use Magento\Catalog\Model\Product\Image\UrlBuilder;
use Magento\Framework\App\Http\Context;
use Magento\Framework\View\ConfigInterface;
use Magento\MultipleWishlist\Block\Customer\Wishlist\Management;
use Magento\Wishlist\Block\Customer\Wishlist\Item\Column;

/**
 * Class Move
 * @package SM\ShoppingList\Block\Customer\Wishlist\Item\Column
 */
class Move extends Column
{
    /**
     * @var Management
     */
    protected $management;

    /**
     * Move constructor.
     * @param Management $management
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param Context $httpContext
     * @param array $data
     * @param ConfigInterface|null $config
     * @param UrlBuilder|null $urlBuilder
     */
    public function __construct(
        Management  $management,
        \Magento\Catalog\Block\Product\Context $context,
        Context $httpContext,
        array $data = [],
        ConfigInterface $config = null,
        UrlBuilder $urlBuilder = null
    ) {
        $this->management = $management;
        parent::__construct($context, $httpContext, $data, $config, $urlBuilder);
    }

    /**
     * @return bool
     */
    public function isDefaultWishlist()
    {
        $wishlist = $this->management->getCurrentWishlist();
        $defaultWishlistId = $this->management->getDefaultWishlist()->getId();
        return $wishlist->getId() == $defaultWishlistId;
    }
}
