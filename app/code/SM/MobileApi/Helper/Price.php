<?php

namespace SM\MobileApi\Helper;

use Magento\Framework\Exception\NoSuchEntityException;

class Price
{
    const PRICE_FORMAT = 'price_format';

    /**
     * @var Data
     */
    protected $dataHelper;

    public function __construct(
        \SM\MobileApi\Helper\Data $mHelper
    ) {
        $this->dataHelper = $mHelper;
    }

    /**
     * Round or not round
     *
     * @param float $value
     *
     * @return float
     * @throws NoSuchEntityException
     */
    public function formatPrice($value)
    {
        if ($this->dataHelper->getConfigValue(Self::PRICE_FORMAT)) {
            return \Zend_Locale_Math::normalize(\Zend_Locale_Math::round($value, 0));
        } else {
            return $value;
        }
    }
}
