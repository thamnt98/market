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


}
