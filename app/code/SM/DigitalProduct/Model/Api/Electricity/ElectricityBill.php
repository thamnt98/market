<?php
/**
 * Class Electricity
 * @package SM\DigitalProduct\Model\Api
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\DigitalProduct\Model\Api\Electricity;

use \SM\DigitalProduct\Api\Processor\ElectricityPostpaidProcessorInterface;

class ElectricityBill extends AbstractInquire implements ElectricityPostpaidProcessorInterface
{
    /**
     * @inheritDoc
     */
    public function inquire($customerId, \Magento\Quote\Api\Data\CartItemInterface $cartItem)
    {
        return parent::inquire($customerId, $cartItem);
    }
}
