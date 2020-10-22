<?php

namespace Wizkunde\ConfigurableBundle\Plugin;

use \Wizkunde\ConfigurableBundle\Block\Catalog\Product\View\Type\Bundle\Option\Single as SingleOption;

class AfterGetOptionsBlock
{


    /**
     * Get the json config with configurable selections
     * @param SingleOption $option
     * @param $configurableOptionsBlock
     * @return false|string
     */
    public function afterGetOptionsBlock(SingleOption $option, $configurableOptionsBlock)
    {
        // Ensure we check if the class exists before trying to add children we may not need
        if(class_exists('Wizkunde\SmartCustomOptions\Block\Product\View\Options\Type\Template')) {
            $configurableOptionsBlock->addChild(
                'template',
                '\Wizkunde\SmartCustomOptions\Block\Product\View\Options\Type\Template',
                [
                    'template' => 'Wizkunde_SmartCustomOptions::options/template.phtml',
                    'bundle_option' => $option->getOption()->getId()
                ]
            );

            $configurableOptionsBlock->addChild(
                'swatch',
                '\Wizkunde\SmartCustomOptions\Block\Product\View\Options\Type\Swatch',
                [
                    'template' => 'Wizkunde_SmartCustomOptions::options/swatch.phtml',
                    'bundle_option' => $option->getOption()->getId()
                ]
            );

            $configurableOptionsBlock->addChild(
                'textswatch',
                '\Wizkunde\SmartCustomOptions\Block\Product\View\Options\Type\Textswatch',
                [
                    'template' => 'Wizkunde_SmartCustomOptions::options/textswatch.phtml',
                    'bundle_option' => $option->getOption()->getId()
                ]
            );

            $configurableOptionsBlock->addChild(
                'image',
                '\Wizkunde\SmartCustomOptions\Block\Product\View\Options\Type\Image',
                [
                    'template' => 'Wizkunde_SmartCustomOptions::options/image.phtml',
                    'bundle_option' => $option->getOption()->getId()
                ]
            );
        }

        return $configurableOptionsBlock;
    }
}
