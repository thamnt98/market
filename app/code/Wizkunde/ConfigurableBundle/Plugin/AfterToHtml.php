<?php

namespace Wizkunde\ConfigurableBundle\Plugin;

class AfterToHtml
{
    private $productRepository = null;

    public function __construct(\Magento\Catalog\Api\ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function afterToHtml(\Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option $option, $html)
    {
        $extraHtml = "";

        foreach ($option->getOption()->getSelections() as $selection) {
            $productModel = $this->productRepository->getById($selection->getProductId());

            if ($option->getLayout()->getBlock('configurable_options_' . $option->getOption()->getId()) == false) {
                if ($productModel->getTypeId() == 'configurable') {
                    $block = $option->getLayout()->createBlock(
                        '\Wizkunde\ConfigurableBundle\Block\Catalog\Product\Renderer\Configurable',
                        'configurable_options_' . $option->getOption()->getId()
                    );
                } else {
                    $block = $option->getLayout()->createBlock(
                        '\Wizkunde\ConfigurableBundle\Block\Catalog\Product\Renderer\Other',
                        'configurable_options_' . $option->getOption()->getId()
                    );
                }

                $configurableOptionsBlock = $block->addChild(
                    'configurableOptions' . $selection->getSelectionId(),
                    'Magento\Catalog\Block\Product\View\Options'
                );

                $configurableOptionsBlock->addChild(
                    'default',
                    '\Wizkunde\ConfigurableBundle\Block\Catalog\Product\View\Options\Type\DefaultType',
                    [
                        'template' => 'Wizkunde_ConfigurableBundle::product/view/options/default.phtml',
                        'bundle_option' => $option->getOption()->getId()
                    ]
                );

                $configurableOptionsBlock->addChild(
                    'text',
                    '\Wizkunde\ConfigurableBundle\Block\Catalog\Product\View\Options\Type\Text',
                    [
                        'template' => 'Wizkunde_ConfigurableBundle::product/view/options/text.phtml',
                        'bundle_option' => $option->getOption()->getId()
                    ]
                );

                $configurableOptionsBlock->addChild(
                    'file',
                    '\Wizkunde\ConfigurableBundle\Block\Catalog\Product\View\Options\Type\File',
                    [
                        'template' => 'Wizkunde_ConfigurableBundle::product/view/options/file.phtml',
                        'bundle_option' => $option->getOption()->getId()
                    ]
                );

                $configurableOptionsBlock->addChild(
                    'select',
                    '\Wizkunde\ConfigurableBundle\Block\Catalog\Product\View\Options\Type\Select',
                    [
                        'template' => 'Wizkunde_ConfigurableBundle::product/view/options/select.phtml',
                        'bundle_option' => $option->getOption()->getId()
                    ]
                );

                $configurableOptionsBlock->addChild(
                    'date',
                    '\Wizkunde\ConfigurableBundle\Block\Catalog\Product\View\Options\Type\Date',
                    [
                        'template' => 'Wizkunde_ConfigurableBundle::product/view/options/date.phtml',
                        'bundle_option' => $option->getOption()->getId()
                    ]
                );
            } else {
                $block = $option->getLayout()->getBlock('configurable_options_' . $option->getOption()->getId());
            }

            $block->setProduct($productModel);
            $block->setOption($option->getOption());

            $extraHtml .= $block->toHtml();
        }

        return $html . $extraHtml;
    }
}
