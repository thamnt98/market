<?php
/**
 * Class ElectrictityBillInterface
 * @package SM\DigitalProduct\Api\Processor\Inquire
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\DigitalProduct\Api\Inquire;

interface ElectricityBillInterface extends InquireInterface
{
    /**
     * @param int $customerId
     * @param string $customerNumber
     * @param int $productId
     * @return \SM\DigitalProduct\Api\Inquire\Data\ElectricityBillInterface
     */
    public function inquire($customerId, $customerNumber, $productId);
}
