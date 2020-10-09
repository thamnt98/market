<?php
/**
 * Class AbstractInquire
 * @package SM\DigitalProduct\Model\Api\Inquire
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\DigitalProduct\Model\Api\Electricity;

class AbstractInquire extends \SM\DigitalProduct\Model\Api\AbstractInquire
{
    /**
     * @inheritDoc
     */
    public function inquire($customerId, \Magento\Quote\Api\Data\CartItemInterface $cartItem)
    {
        list($customerNumber, $productId) = $this->prepareInquireData($cartItem);
        return $this->inquireClass->{$this->inquireMethod}($customerId, $customerNumber, $productId);
    }

    /**
     * @param \Magento\Quote\Api\Data\CartItemInterface $cartItem
     * @return array
     */
    protected function prepareInquireData(\Magento\Quote\Api\Data\CartItemInterface $cartItem)
    {
        $data = [];
        if ($cartItem->getProductOption()
            && $cartItem->getProductOption()->getExtensionAttributes()) {
            $buyRequest = $cartItem->getProductOption()->getExtensionAttributes();
            $data[] = $buyRequest->getDigital()->getData('customer_id');
            $data[] = $buyRequest->getDigitalTransaction()->getData('product_id_vendor');
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function createTransaction($data)
    {
        // TODO: Implement createTransaction() method.
    }
}
