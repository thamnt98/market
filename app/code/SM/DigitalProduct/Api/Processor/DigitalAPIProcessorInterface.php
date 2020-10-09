<?php
/**
 * Class DigitalAPIProcessorInterface
 * @package SM\DigitalProduct\Api
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\DigitalProduct\Api\Processor;

use Magento\Quote\Api\Data\CartItemInterface;

interface DigitalAPIProcessorInterface
{
    /**
     * Reorder Inquire
     *
     * @param $customerId
     * @param CartItemInterface $cartItem
     * @return \SM\DigitalProduct\Api\DigitalProductRepositoryInterface
     */
    public function inquire($customerId, CartItemInterface $cartItem);

    /**
     * @param array $data
     * @return mixed
     */
    public function createTransaction($data);
}
