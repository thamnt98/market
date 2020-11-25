<?php

namespace SM\Checkout\Preference\Amasty\Promo\Plugin\Model\CustomerData;

use Amasty\Promo\Helper\Item;
use Magento\Catalog\Model\ResourceModel\Url;
use Magento\Checkout\CustomerData\ItemPoolInterface;
use Magento\Checkout\Model\Session;

class Cart extends \Amasty\Promo\Plugin\Model\CustomerData\Cart
{
    /**
     * @var Item
     */
    protected $promoItemHelper;

    /**
     * @var ItemPoolInterface
     */
    protected $itemPoolInterface;

    /**
     * @var Url
     */
    protected $catalogUrl;

    public function __construct(Item $promoItemHelper, ItemPoolInterface $itemPoolInterface, Url $catalogUrl, Session $checkoutSession)
    {
        parent::__construct($promoItemHelper, $itemPoolInterface, $catalogUrl, $checkoutSession);
        $this->promoItemHelper = $promoItemHelper;
        $this->itemPoolInterface = $itemPoolInterface;
        $this->catalogUrl = $catalogUrl;
    }

    /**
     * Get array of last added items
     *
     * @param \Magento\Checkout\CustomerData\Cart $cart
     * @param array $sectionData
     *
     * @return \Magento\Quote\Model\Quote\Item[]
     * @codingStandardsIgnoreStart
     */
    protected function getRecentItems(\Magento\Checkout\CustomerData\Cart $cart, $sectionData)
    {
        $items = [];
        foreach (array_reverse($this->getAllQuoteItems($cart)) as $item) {
            /** @var $item \Magento\Quote\Model\Quote\Item */
            if (!$item->getProduct()->isVisibleInSiteVisibility()) {
                $product =  $item->getOptionByCode('product_type') !== null
                    ? $item->getOptionByCode('product_type')->getProduct()
                    : $item->getProduct();

                if (!$this->promoItemHelper->isPromoItem($item)) {
                    $products = $this->catalogUrl->getRewriteByProductStore([$product->getId() => $item->getStoreId()]);

                    if (!isset($products[$product->getId()])) {
                        continue;
                    }

                    $urlDataObject = new \Magento\Framework\DataObject($products[$product->getId()]);
                    $item->getProduct()->setUrlDataObject($urlDataObject);
                }
            }
            $items[] = $this->itemPoolInterface->getItemData($item);
        }

        return $items;
    }

    /**
     * Return customer quote items
     *
     * @param \Magento\Checkout\CustomerData\Cart $cart
     *
     * @return \Magento\Quote\Model\Quote\Item[]
     */
    protected function getAllQuoteItems(\Magento\Checkout\CustomerData\Cart $cart)
    {
        if ($cart->getCustomQuote()) {
            return $cart->getCustomQuote()->getAllVisibleItems();
        }

        return $this->getAllVisibleItems();
    }

    /**
     * Get array of all items what can be display directly
     *
     * @return \Magento\Quote\Model\Quote\Item[]
     */
    protected function getAllVisibleItems()
    {
        $items = [];
        foreach ($this->getQuote()->getItemsCollection() as $item) {
            if (!$item->isDeleted() && !$item->getParentItemId() && !$item->getParentItem()) {
                if ($item->getProduct()->getTypeId() == 'virtual') {
                    continue;
                }
                $items[] = $item;
            }
        }
        return $items;
    }
}
