<?php
/**
 * Class PaymentMethods
 * @package SM\Checkout\Model\Api\CheckoutData
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Checkout\Model\Api\CheckoutData;

use Magento\Framework\Api\AbstractSimpleObject;
use SM\Checkout\Api\Data\Checkout\SupportShippingInterface;

class SupportShipping extends AbstractSimpleObject implements SupportShippingInterface
{
    const USE = 'use';
    const MESSAGE = 'message';
    const ADDRESS_MESSAGE = 'address_message';
    const ADDRESS_SUPPORT = 'address_support';

    /**
     * @inheritDoc
     */
    public function setUse($use)
    {
        return $this->setData(self::USE, $use);
    }

    /**
     * @inheritDoc
     */
    public function getUse()
    {
        return $this->_get(self::USE);
    }

    /**
     * @inheritDoc
     */
    public function setMessage($message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

    /**
     * @inheritDoc
     */
    public function getMessage()
    {
        return $this->_get(self::MESSAGE);
    }

    /**
     * @inheritDoc
     */
    public function setAddressMessage($addressMassage)
    {
        return $this->setData(self::ADDRESS_MESSAGE, $addressMassage);
    }

    /**
     * @inheritDoc
     */
    public function getAddressMessage()
    {
        return $this->_get(self::ADDRESS_MESSAGE);
    }

    /**
     * @inheritDoc
     */
    public function setAddressSupport($addressSupport)
    {
        return $this->setData(self::ADDRESS_SUPPORT, $addressSupport);
    }

    /**
     * @inheritDoc
     */
    public function getAddressSupport()
    {
        return $this->_get(self::ADDRESS_SUPPORT);
    }
}
