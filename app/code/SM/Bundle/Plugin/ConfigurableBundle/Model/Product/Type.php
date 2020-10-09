<?php

namespace SM\Bundle\Plugin\ConfigurableBundle\Model\Product;

use Wizkunde\ConfigurableBundle\Model\Product\Type as BundleType;

class Type
{
    /**
     * @param BundleType $subject
     * @param \Magento\Framework\DataObject $buyRequest
     * @param $product
     * @param string $processMode
     * @return array
     */
    public function beforePrepareForCartAdvanced(
        BundleType $subject,
        \Magento\Framework\DataObject $buyRequest,
        $product,
        $processMode = BundleType::PROCESS_MODE_FULL
    ) {
        if ($product->getTypeId() == 'bundle' && !array_key_exists('super_attribute', $buyRequest->getData())) {
            if ($buyRequest->getData('_processing_params') &&
                $buyRequest->getData('_processing_params')->getData('current_config') &&
                $buyRequest->getData('_processing_params')->getData('current_config')->getData('super_attribute')
            ) {
                $buyRequest->setData('super_attribute', $buyRequest->getData('_processing_params')->getData('current_config')->getData('super_attribute'));
            }
        }
        return [$buyRequest, $product, $processMode];
    }
}
