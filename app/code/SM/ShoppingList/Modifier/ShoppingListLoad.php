<?php
/**
 * @category Magento
 * @package SM\ShoppingList\Modifier
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\ShoppingList\Modifier;

use Magento\Framework\Exception\LocalizedException;
use Magento\Wishlist\Model\Wishlist;

/**
 * Class ShoppingListLoad
 * @package SM\ShoppingList\Modifier
 */
class ShoppingListLoad extends Wishlist
{
    /**
     * Load by sharing code
     *
     * @param string $code
     * @return $this
     * @throws LocalizedException
     */
    public function loadByCode($code)
    {
        $this->_getResource()->load($this, $code, 'sharing_code');
        if (!$this->getShared()) {
            $this->setShared(1)->save();
            return $this;
        }

        return $this;
    }
}
