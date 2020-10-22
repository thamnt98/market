<?php

namespace Wizkunde\ConfigurableBundle\Plugin;

class AfterConfigurablePrepareForCart
{
    /**
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableProduct
     * @param $result
     * @return mixed
     */
    public function afterPrepareForCart(\Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableProduct, $result)
    {
        /**
         * If its a configurable product with a parent (in a bundle)
         * Copy the custom options from the simple to the configurable
         */

        if(is_array($result[0]) && $result[0]->getParentProductId() && $result[1]->getCustomOption('option_ids'))
        {
            $optionIds = $result[1]->getCustomOption('option_ids');
            $result[0]->addCustomOption('option_ids', $optionIds->getValue());

            $optionIds = explode(',', $optionIds->getValue());
            foreach ($optionIds as $optionId) {
                if ($option = $result[1]->getCustomOption('option_' . $optionId)) {
                    $result[0]->addCustomOption('option_' . $optionId, $option->getValue());
                }
            }
        }

        return $result;
    }
}
