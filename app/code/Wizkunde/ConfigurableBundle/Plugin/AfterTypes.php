<?php

namespace Wizkunde\ConfigurableBundle\Plugin;

class AfterTypes
{
    /**
     * Add configurables to the allowed types
     *
     * @param array $types
     * @return array
     */
    public function afterGetAllowedSelectionTypes(\Magento\Bundle\Helper\Data $bundleHelper, $types)
    {
        $types['configurable'] = 'configurable';
        $types['downloadable'] = 'downloadable';

        return $types;
    }
}
