<?php
/**
 * Class ElectrictityTokenInterface
 * @package SM\DigitalProduct\Api\Processor\Inquire
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\DigitalProduct\Api\Inquire;

interface ElectricityTokenInterface extends InquireInterface
{
    /**
     * @param int $customerId
     * @param string $customerNumber
     * @param int $productId
     * @return \SM\DigitalProduct\Api\Inquire\Data\ElectricityTokenInterface
     */
    public function inquire($customerId, $customerNumber, $productId);
}
