<?php
/**
 * Class Search
 * @package SM\Checkout\Plugin\Quote
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\Checkout\Plugin\Quote\Model\Quote;

class Address
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * Address constructor.
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address $subject
     * @return array
     */
    public function beforeCollectShippingRates(
        \Magento\Quote\Model\Quote\Address $subject
    ) {
        if ($this->checkoutSession->getMainOrder() && $this->checkoutSession->getIsMultipleShippingAddresses()) {
            $subject->setCollectShippingRates(false);
        }
        return [];
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address $subject
     * @param $result
     * @return mixed
     */
    public function afterCollectShippingRates(
        \Magento\Quote\Model\Quote\Address $subject,
        $result
    ) {
        if ($this->checkoutSession->getMainOrder() && $this->checkoutSession->getIsMultipleShippingAddresses()) {
            $subject->setCollectShippingRates(true);
        }
        return $result;
    }
}
