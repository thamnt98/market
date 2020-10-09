<?php
/**
 * Class HowToPay
 * @package SM\Checkout\Model\Api\CheckoutData\PaymentMethods
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Checkout\Model\Api\CheckoutData\PaymentMethods;

use Magento\Framework\DataObject;
use SM\Checkout\Api\Data\Checkout\PaymentMethods\HowToPayInterface;

class HowToPay extends DataObject implements HowToPayInterface
{
    /**
     * @inheritDoc
     */
    public function getBlockTitle()
    {
        return $this->getData(self::BLOCK_TITLE);
    }

    /**
     * @inheritDoc
     */
    public function setBlockTitle($value)
    {
        return $this->setData(self::BLOCK_TITLE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getBlockContent()
    {
        return $this->getData(self::BLOCK_CONTENT);
    }

    /**
     * @inheritDoc
     */
    public function setBlockContent($value)
    {
        return $this->setData(self::BLOCK_CONTENT, $value);
    }
}
