<?php


namespace SM\Checkout\Block\Cart\Item\Price;


use Magento\Framework\Pricing\PriceCurrencyInterface;
use SM\Checkout\Model\Price;

class Renderer extends \Magento\Weee\Block\Item\Price\Renderer
{

    /**
     * @var Price
     */
    protected $price;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Tax\Helper\Data $taxHelper,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Weee\Helper\Data $weeeHelper,
        Price $price,
        array $data = []
    ) {
        parent::__construct($context, $taxHelper, $priceCurrency, $weeeHelper, $data);
        $this->price = $price;
    }
    public function getRegularPrice($product)
    {
        return $this->price->getRegularPrice($product);
    }
}
