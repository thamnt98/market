<?php
/**
 * @category Magento
 * @package SM\ShoppingList\Block
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\ShoppingList\Block;

use Magento\Framework\View\Element\Template;
use SM\ShoppingList\Api\Data\ShoppingListDataInterface;

/**
 * Class Toolbar
 * @package SM\ShoppingList\Block
 */
class Toolbar extends Template
{
    /**
     * @var ShoppingListDataInterface
     */
    protected $shoppingList;

    /**
     * @return ShoppingListDataInterface
     */
    public function getShoppingList()
    {
        return $this->shoppingList;
    }

    /**
     * @param ShoppingListDataInterface $shoppingList
     * @return $this
     */
    public function setShoppingList($shoppingList)
    {
        $this->shoppingList = $shoppingList;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        return strtok($this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]), "?");
    }

    /**
     * @return string
     */
    public function getUpdateUrl()
    {
        return $this->getUrl("shoppinglist/ajax/updatelist");
    }

    /**
     * @param int $listId
     * @return string
     */
    public function getRemoveListUrl($listId)
    {
        return $this->getUrl("shoppinglist/action/removelist", ["id" => $listId]);
    }

    /**
     * @return string
     */
    public function getShareUrl()
    {
        return $this->getUrl(
            "shoppinglist/shared/index",
            ["code" => $this->getShoppingList()->getSharingCode()]
        );
    }
}
