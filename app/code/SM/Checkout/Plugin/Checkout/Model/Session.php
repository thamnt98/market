<?php
/**
 * Class Session
 * @package SM\Checkout\Plugin\Checkout\Model
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Checkout\Plugin\Checkout\Model;

class Session
{
    /**
     * @var \SM\Checkout\Helper\DigitalProduct
     */
    private $digitalHelper;

    public function __construct(
        \SM\Checkout\Helper\DigitalProduct $digitalHelper
    ) {
        $this->digitalHelper = $digitalHelper;
    }

    /**
     * @param $subject
     * @param $result
     * @return \Magento\Quote\Model\Quote
     */
    public function afterGetQuote(\Magento\Checkout\Model\Session $subject, $result)
    {
        if (!$this->digitalHelper->isFromDigital() && !$this->digitalHelper->isAjax()) {
            $this->digitalHelper->setIsDigitalSession(0);
            return $result->setIsVirtual($this->digitalHelper->getIsDigitalSession());
        }

        return $result;
    }
}
