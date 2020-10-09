<?php

declare(strict_types=1);

namespace SM\Checkout\Plugin\Quote\Model\Quote;

class Item
{
    /**
     * @var \SM\Checkout\Model\Price
     */
    protected $price;

    /**
     * Item constructor.
     * @param \SM\Checkout\Model\Price $price
     */
    public function __construct(
        \SM\Checkout\Model\Price $price
    ) {
        $this->price = $price;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $subject
     * @param $result
     * @return mixed
     */
    public function afterToArray(
        \Magento\Quote\Model\Quote\Item $subject,
        $result
    ) {
        $result['regular_price'] = $this->price->getRegularPrice($subject->getProduct());
        return $result;
    }


}
