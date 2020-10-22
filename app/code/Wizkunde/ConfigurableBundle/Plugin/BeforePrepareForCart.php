<?php

namespace Wizkunde\ConfigurableBundle\Plugin;

use Magento\Catalog\Api\ProductRepositoryInterface;

class BeforePrepareForCart
{
    private $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function beforePrepareForCartAdvanced(
        $product,
        \Magento\Framework\DataObject $buyRequest,
        $selection,
        $processMode = null
    ) {
        $this->unsetEmptyItems($buyRequest);

        if ($buyRequest->getOsa() !== null) {
            $buyRequest->setSuperAttribute($buyRequest->getOsa());
        }

        $attributes = $buyRequest->getSuperAttribute();

        if (isset($attributes[$selection->getOptionId()])) {
            $buyRequest->setOsa($buyRequest->getSuperAttribute());

            if(isset($attributes[$selection->getOptionId()])) {
                if(isset($attributes[$selection->getOptionId()][$selection->getSelectionId()])) {
                    $buyRequest->setSuperAttribute($attributes[$selection->getOptionId()][$selection->getSelectionId()]);
                } else {
                    $buyRequest->setSuperAttribute($attributes[$selection->getOptionId()]);
                }
            }
        }

        $bundleOptions = $buyRequest->getBundleCustomOptions();

        if (is_array($bundleOptions) && isset($bundleOptions[$selection->getOptionId()])) {
            $optionData = $bundleOptions[$selection->getOptionId()];

            $selection->addCustomOption('option_ids', implode(',', array_keys($optionData)));

            foreach($optionData as $optionKey => $optionValue)
            {
                if($optionData[$optionKey] == '')
                {
                    unset($optionData[$optionKey]);
                }
            }

            foreach($optionData as $optionKey => $optionValue)
            {
                $selection->addCustomOption('option_' . $optionKey, $optionValue);
            }

            $buyRequest->addData(['options' => $optionData]);
        }

        return [$buyRequest, $selection, $processMode];
    }

    /**
     * Unset all empty items
     *
     * @param \Magento\Framework\DataObject $buyRequest
     */
    protected function unsetEmptyItems(\Magento\Framework\DataObject $buyRequest)
    {
        $qtys = $buyRequest->getBundleOptionQty();
        $optionData = $buyRequest->getBundleOption();
        $superAttributes = $buyRequest->getSuperAttribute();

        if (is_array($qtys)) {
            foreach ($qtys as $id => $value) {
                if ($value == 0) {
                    unset($qtys[$id]);
                    unset($optionData[$id]);
                    unset($superAttributes[$id]);
                }
            }
        }

        $buyRequest->setBundleOptionQty($qtys);
        $buyRequest->setBundleOption($optionData);
        $buyRequest->setSuperAttribute($superAttributes);
    }
}
