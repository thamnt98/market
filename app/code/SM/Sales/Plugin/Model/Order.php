<?php
/**
 * Class Order
 * @package SM\Sales\Plugin\Model
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Sales\Plugin\Model;

class Order
{
    /**
     * @param $subject
     * @param callable $proceed
     * @return bool
     */
    public function aroundCanCancel(\Magento\Sales\Model\Order $subject, callable $proceed)
    {
        if ($subject->getIsParent()) {
            return false;
        }

        return $proceed();
    }
}
