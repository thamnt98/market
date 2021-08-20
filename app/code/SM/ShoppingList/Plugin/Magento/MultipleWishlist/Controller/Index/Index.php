<?php
/**
 * SM\ShoppingList\Plugin\Magento\MultipleWishlist\Controller\Index
 *
 * @copyright Copyright © 2021 SmartOSC. All rights reserved.
 * @author    hungnv6@smartosc.com
 */

namespace SM\ShoppingList\Plugin\Magento\MultipleWishlist\Controller\Index;

use Magento\Framework\App\ViewInterface;
use Magento\Framework\UrlInterface;
use Magento\MultipleWishlist\Helper\Data;
use Magento\Wishlist\Model\Wishlist;
use Magento\Wishlist\Model\WishlistFactory;

/**
 * Class Index
 * @package SM\ShoppingList\Plugin\Magento\MultipleWishlist\Controller\Index
 */
class Index
{
    /**
     * @var Data
     */
    protected $wishlistData;

    /**
     * @var WishlistFactory
     */
    protected $wishlistFactory;

    /**
     * @var ViewInterface
     */
    protected $view;

    /**
     * @var UrlInterface
     */
    protected $url;

    /**
     * Index constructor.
     * @param Data $wishlistData
     * @param WishlistFactory $wishlistFactory
     * @param ViewInterface $view
     * @param UrlInterface $url
     */
    public function __construct(
        Data $wishlistData,
        WishlistFactory $wishlistFactory,
        ViewInterface $view,
        UrlInterface $url
    ) {
        $this->view = $view;
        $this->url = $url;
        $this->wishlistData = $wishlistData;
        $this->wishlistFactory = $wishlistFactory;
    }

    /**
     * @param \Magento\MultipleWishlist\Controller\Index\Index $subject
     */
    public function afterExecute($subject, $result)
    {
        if ($this->wishlistData->isMultipleEnabled()) {
            $wishlistId = $subject->getRequest()->getParam('wishlist_id');
            /** @var \Magento\Theme\Block\Html\Breadcrumbs $breadcrumbs */
            $breadcrumbs = $this->view->getLayout()->getBlock('breadcrumbs');
            if ($wishlistId && $wishlistId != $this->wishlistData->getDefaultWishlist()->getId()) {
                $this->addBreadCrumb($breadcrumbs, $this->wishlistFactory->create()->load($wishlistId));
            } else {
                $this->addIndexCrumb($breadcrumbs);
            }
        }

        return $result;
    }

    public function addIndexCrumb($breadcrumbs)
    {
        $breadcrumbs->addCrumb(
            'shopping_list',
            [
                'label' => __("Shopping List"),
                'title' => __("Shopping List")
            ]
        );
    }

    /**
     * Add breadcrumb for list name
     *
     * @param Wishlist $shoppingList
     */
    public function addBreadCrumb($breadcrumbs, $shoppingList)
    {
        if ($breadcrumbs) {
            $breadcrumbs->addCrumb(
                'shopping_list',
                [
                    'label' => __("Shopping List"),
                    'title' => __("Shopping List"),
                    'link' => $this->url->getUrl("wishlist")
                ]
            );
            $breadcrumbs->addCrumb(
                'my_lists',
                [
                    'label' => __("My Lists"),
                    'title' => __("My Lists"),
                    'link' => $this->url->getUrl("wishlist/mylist")
                ]
            );
            $breadcrumbs->addCrumb(
                'list_name',
                [
                    'label' => $shoppingList->getName(),
                    'title' => $shoppingList->getName()
                ]
            );
        }
    }
}
