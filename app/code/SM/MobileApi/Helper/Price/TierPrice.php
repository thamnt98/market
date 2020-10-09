<?php

namespace SM\MobileApi\Helper\Price;

/**
 * Class TierPrice
 * @package SM\MobileApi\Helper\Price
 */
class TierPrice
{
    protected $objectManager;

    /**
     * TierPrice constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Return tier prices data
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return array
     */
    public function getTierPrices($product)
    {
        if (! $product || ! $product->getId()) {
            return null;
        }

        /** @var \Magento\Catalog\Pricing\Price\TierPrice $tierPriceModel */
        $tierPriceModel = $this->objectManager->create('\Magento\Catalog\Pricing\Price\TierPrice', [
            'saleableItem' => $product,
            'quantity'     => null
        ]);

        $data       = [];
        $tierPrices = $tierPriceModel->getTierPriceList();
        foreach ($tierPrices as $price) {
            $data[] = [
                'price'        => ($price['price'])->getValue(),
                'qty'          => $price['price_qty'],
                'save_percent' => $tierPriceModel->getSavePercent($price['price'])
            ];
        }

        return $data;
    }
}
