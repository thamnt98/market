<?php
/**
 * @category Magento
 * @package SM\ShoppingList\Block\Item\Column
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\ShoppingList\Block\Item\Column;

use Magento\Catalog\Model\Product;
use Magento\Framework\View\Element\Template;
use SM\Catalog\Block\Product\ProductList\Item\AddTo\Iteminfo;
use SM\ShoppingList\Api\Data\ShoppingListDataInterface;
use SM\ShoppingList\Api\Data\ShoppingListItemDataInterface;
use SM\ShoppingList\Block\Item\Column;
use SM\Label\Model\LabelViewer;
use Magento\Framework\Registry;

/**
 * Class Actions
 * @package SM\ShoppingList\Block\Item\Column
 */
class Actions extends Column
{
    /**
     * @var ShoppingListDataInterface[]
     */
    protected $shoppingLists;
    /**
     * @var Iteminfo
     */
    protected $itemInfo;
    /**
     * @var ShoppingListItemDataInterface[]
     */
    protected $items;

    /**
     * Actions constructor.
     * @param Template\Context $context
     * @param LabelViewer $helper
     * @param Registry $registry
     * @param Iteminfo $itemInfo
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        LabelViewer $helper,
        Registry $registry,
        Iteminfo $itemInfo,
        array $data = []
    ) {
        $this->itemInfo = $itemInfo;
        parent::__construct($context, $helper, $registry, $data);
    }

    /**
     * @return ShoppingListItemDataInterface[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param ShoppingListItemDataInterface[] $items
     * @return Actions
     */
    public function setItems($items)
    {
        $this->items = $items;
        return $this;
    }

    /**
     * @return ShoppingListDataInterface[]
     */
    public function getShoppingLists()
    {
        return $this->shoppingLists;
    }

    /**
     * @param ShoppingListDataInterface[] $shoppingLists
     * @return Actions
     */
    public function setShoppingLists($shoppingLists)
    {
        $this->shoppingLists = $shoppingLists;
        return $this;
    }

    /**
     * @return array
     */
    public function getItemIds()
    {
        $item_ids = [];
        /** @var ShoppingListItemDataInterface $item */
        foreach ($this->getItems() as $item) {
            $item_ids[] = $item->getWishlistItemId();
        }
        return $item_ids;
    }

    /**
     * @return string
     */
    public function getMoveItemUrl()
    {
        return $this->getUrl("shoppinglist/ajax/moveitem");
    }

    /**
     * @param Product $product
     * @return float|null
     */
    public function getDiscountPercent($product)
    {
        if ($product != null) {
            return $this->itemInfo->getDiscountPercent($product);
        }
        return null;
    }

    /**
     * @param int $itemId
     * @return string
     */
    public function getRemoveUrl($itemId)
    {
        return $this->getUrl("shoppinglist/action/removeitem", ["id" => $itemId]);
    }

    /**
     * @return string
     */
    public function getCreatListUrl()
    {
        return $this->getUrl("shoppinglist/ajax/createlist");
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }
}
