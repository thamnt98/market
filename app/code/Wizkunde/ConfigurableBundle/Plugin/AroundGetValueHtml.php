<?php

namespace Wizkunde\ConfigurableBundle\Plugin;

class AroundGetValueHtml
{
    protected $productRepository;

    /**
     * AroundGetValueHtml constructor.
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(\Magento\Catalog\Api\ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function aroundGetValueHtml(
        \Magento\Sales\Block\Adminhtml\Items\Renderer\DefaultRenderer $renderer,
        \Closure $proceed,
        $item
    ) {
        $attributeData = "";

        // Render the SKU in the backend
        if ($renderer->getArea() == 'adminhtml') {
            $attributeData .= '<br /><span>SKU: ' . $item->getSku() . '</span>';
        }

        if ($item->getProductOptions()) {
            $productOptions = $item->getProductOptions();

            if (isset($productOptions['attributes_info'])) {
                foreach ($productOptions['attributes_info'] as $attribute) {
                    $attributeData .= '<br /><i style="margin-left: 14px;">' . $attribute['label'] . ': ' . $attribute['value'] . '</i>';
                }
            }

            if (isset($productOptions['simple_sku']) && isset($productOptions['info_buyRequest']['options'])) {
                $confProduct = $this->productRepository->getById($item->getProductId());
                $simpleProduct = $confProduct->getTypeInstance()->getProductByAttributes($productOptions['info_buyRequest']['super_attribute'], $confProduct);

                foreach($simpleProduct->getOptions() as $option)
                {
                    if(isset($productOptions['info_buyRequest']['options'][$option->getId()]))
                    {
                        foreach($option->getValues() as $value)
                        {
                            if($value->getId() == $productOptions['info_buyRequest']['options'][$option->getId()])
                            {
                                $attributeData .= '<br /><i style="margin-left: 14px;">' . $option->getTitle() . '</i>: ' . $value->getTitle();
                            }
                        }
                    }
                }
            }
        }

        $returnValue = $proceed($item);

        return $returnValue . $attributeData;
    }
}
