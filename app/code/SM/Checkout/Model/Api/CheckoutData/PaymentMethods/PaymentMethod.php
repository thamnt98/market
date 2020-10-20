<?php
/**
 * Class PaymentMethod
 * @package SM\Checkout\Model\Api\CheckoutData\PaymentMethods
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Checkout\Model\Api\CheckoutData\PaymentMethods;

use Magento\Framework\Api\AbstractSimpleObject;
use SM\Checkout\Api\Data\Checkout\PaymentMethods\PaymentMethodInterface;

class PaymentMethod extends \Magento\Framework\Model\AbstractExtensibleModel implements PaymentMethodInterface
{

    public function getTitle()
    {
        return $this->getData('title');
    }

    public function setTitle($data)
    {
        return $this->setData('title', $data);
    }

    public function getDescription()
    {
        return $this->getData('description');
    }

    public function setDescription($data)
    {
        return $this->setData('description', $data);
    }

    public function getMethods()
    {
        return $this->getData('methods');
    }

    public function setMethods($data)
    {
        return $this->setData('methods', $data);
    }

    public function setCardType($data)
    {
        return $this->setData('card_type', $data);
    }

    public function getCardType()
    {
        return $this->getData('card_type');
    }
}
