<?php

namespace Wizkunde\ConfigurableBundle\Plugin;

class LayoutPlugin
{
    protected $scopeConfig;
    protected $state;
    protected $request;
    protected $registry;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\State $state,
        \Magento\Framework\Registry $registry
    ) {
    
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
        $this->state = $state;
        $this->registry = $registry;
    }

    public function beforeGenerateXml(\Magento\Framework\View\Layout $layout)
    {
        if ($this->state->getAreaCode() == 'frontend' && $this->request->getFullActionName() == 'catalog_product_view') {
            $product = $this->registry->registry('current_product');

            if ($product && $product->getTypeId() == 'bundle') {
                $this->updateLayout($layout);
            }
        }
    }

    public function updateLayout($layout)
    {
        $update = $layout->getUpdate();

        if ($this->scopeConfig->getValue('configurablebundle/general/move_options')) {
            $update->addUpdate('<head><css src="Wizkunde_ConfigurableBundle::css/options_top.css" /></head>');
            $update->addUpdate('<move element="product.info" destination="product.info.main" before="product.info.price" />');
            $update->addUpdate('<move element="product.info.overview" destination="product.info.main" before="product.info" />');
            $update->addupdate('<referenceBlock name="product.info.price" remove="true" />');
            $update->addUpdate('<referenceBlock name="bundle.summary" class="Magento\Catalog\Block\Product\View" as="form_bottom" template="Wizkunde_ConfigurableBundle::catalog/product/view/summary_top.phtml" />');
            $update->addUpdate('<move element="product.info.addtocart" destination="product.info" after="-" />');
            $update->addUpdate('<move element="product.price.render.bundle.customization" destination="product.info.main" before="product.info" />');
        }
    }
}
