<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Wizkunde\ConfigurableBundle\Block\Catalog\Product\View\Type\Bundle\Option;

class Single extends \Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option\Select
{
    protected $_template = 'product/view/type/bundle/option/single.phtml';

    protected $configurableRenderer = null;
    protected $otherRenderer = null;
    protected $jsonDecoder = null;
    protected $productRepository = null;
    protected $productOptionRepository = null;
    protected $imageFactory = null;
    protected $quoteItemOption;
    protected $optionsFactory;
    protected $bundleHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Json\DecoderInterface $jsonDecoder
     * @param \Magento\Catalog\Model\ProductRepository $productRepository ,
     * @param \Magento\Catalog\Model\Product\Option\Repository $productOptionRepository ,
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Catalog\Helper\ImageFactory $imageFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Framework\Math\Random $mathRandom
     * @param \Magento\Checkout\Helper\Cart $cartHelper
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param \Wizkunde\ConfigurableBundle\Block\Catalog\Product\Renderer\Configurable
     * @param \Wizkunde\ConfigurableBundle\Block\Catalog\Product\Renderer\Other $otherRenderer
     * @param \Magento\Quote\Model\Quote\Item\OptionFactory $quoteItemOption
     * @param \Magento\Catalog\Block\Product\View\OptionsFactory $optionsFactory
     * @param \Wizkunde\ConfigurableBundle\Helper\Bundle $bundleHelper
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Json\DecoderInterface $jsonDecoder,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Model\Product\Option\Repository $productOptionRepository,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Catalog\Helper\ImageFactory $imageFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\Math\Random $mathRandom,
        \Magento\Checkout\Helper\Cart $cartHelper,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Wizkunde\ConfigurableBundle\Block\Catalog\Product\Renderer\Configurable $configurableRenderer,
        \Wizkunde\ConfigurableBundle\Block\Catalog\Product\Renderer\Other $otherRenderer,
        \Magento\Quote\Model\Quote\Item\OptionFactory $quoteItemOption,
        \Magento\Catalog\Block\Product\View\OptionsFactory $optionsFactory,
        \Wizkunde\ConfigurableBundle\Helper\Bundle $bundleHelper,
        array $data = []
    ) {
        $this->pricingHelper = $pricingHelper;
        $this->_catalogHelper = $catalogData;
        $this->_taxHelper = $taxData;

        $this->otherRenderer = $otherRenderer;
        $this->configurableRenderer = $configurableRenderer;
        $this->jsonDecoder = $jsonDecoder;
        $this->productRepository = $productRepository;
        $this->productOptionRepository = $productOptionRepository;
        $this->quoteItemOption = $quoteItemOption;
        $this->imageFactory = $imageFactory;
        $this->optionsFactory = $optionsFactory;
        $this->bundleHelper = $bundleHelper;

        parent::__construct(
            $context,
            $jsonEncoder,
            $catalogData,
            $registry,
            $string,
            $mathRandom,
            $cartHelper,
            $taxData,
            $pricingHelper,
            $data
        );
    }

    public function getProductThumbnail($product, $width = 100, $height = 100)
    {
        return $this->imageFactory->create()->init(
            $product,
            'product_small_image',
            [
                'width' => $width,
                'height' => $height
            ]
        )->getUrl();
    }

    /**
     * @return string
     */
    public function getDefaultSelectionId()
    {
        foreach ($this->getOption()->getSelections() as $selection) {
            if ($this->isSelected($selection)) {
                return $selection->getSelectionId();
            }
        }

        return 0;
    }

    public function isConfigurableSelection($selection)
    {
        if ($selection->getTypeId() == 'configurable') {
            return true;
        }

        return false;
    }

    public function getConfigurableRenderer($selection)
    {
        if($this->isConfigurableSelection($selection)) {
            $configurableProduct = $this->productRepository->getById($selection->getProductId());
            $configurableRenderer = clone($this->configurableRenderer);
            $configurableRenderer->setProduct($configurableProduct);

            return $configurableRenderer;
        }

        return null;
    }

    /**
     * @return string
     */
    public function getJsonConfig($selection)
    {
        if($this->isConfigurableSelection($selection)) {
            $configurableProduct = $this->productRepository->getById($selection->getProductId());
            $configurableRenderer = clone($this->configurableRenderer);
            $configurableRenderer->setProduct($configurableProduct);
            return $configurableRenderer->getJsonConfig();
        }
    }

    /**
     * @return string
     */
    public function getJsonSwatchConfig($selection)
    {
        if($this->isConfigurableSelection($selection)) {
            $configurableProduct = $this->productRepository->getById($selection->getProductId());

            $configurableRenderer = clone($this->configurableRenderer);
            $configurableRenderer->setProduct($configurableProduct);
            return $configurableRenderer->getJsonSwatchConfig();
        }

        return '{}';
    }

    public function decorateArray($data)
    {
        return $this->configurableRenderer->decorateArray($data);
    }

    public function getBundleHelper()
    {
        return $this->bundleHelper;
    }
    
    public function getConfigurableOptions($selection = null, $serialize = false)
    {
        $configurableOptionsBlock = $this->getOptionsBlock($selection);

        $productModel = $this->productRepository->getById($selection->getProductId());
        $configurableOptionsBlock->setProduct($productModel);

        if ($serialize == false) {
            $optionHtml = '';

            foreach ($this->productOptionRepository->getProductOptions($productModel) as $_option) {
                $optionHtml .= $configurableOptionsBlock->getOptionHtml($_option);
            }

            return $optionHtml;
        } else {
            return $configurableOptionsBlock->getJsonConfig();
        }
    }

    public function getOptionsBlock($selection = null)
    {
        if ($this->getLayout()->isBlock('configurableOptions' . $selection->getSelectionId())) {
            $this->getLayout()->unsetElement('configurableOptions' . $selection->getSelectionId());
        }
    
        $configurableOptionsBlock = $this->getLayout()->createBlock(
            'Magento\Catalog\Block\Product\View\Options',
            'configurableOptions' . $selection->getSelectionId()
        );
        $super_opt = $this->getSuperOptions();

        $configurableOptionsBlock->addChild(
            'default',
            '\Wizkunde\ConfigurableBundle\Block\Catalog\Product\View\Options\Type\DefaultType',
            [
                'template' => 'Wizkunde_ConfigurableBundle::product/view/options/default.phtml',
                'bundle_option' => $this->getOption()->getId()
            ]
        );

        $configurableOptionsBlock->addChild(
            'text',
            '\Wizkunde\ConfigurableBundle\Block\Catalog\Product\View\Options\Type\Text',
            [
                'template' => 'Wizkunde_ConfigurableBundle::product/view/options/text.phtml',
                'bundle_option' => $this->getOption()->getId()
            ]
        );

        $configurableOptionsBlock->addChild(
            'file',
            '\Wizkunde\ConfigurableBundle\Block\Catalog\Product\View\Options\Type\File',
            [
                'template' => 'Wizkunde_ConfigurableBundle::product/view/options/file.phtml',
                'bundle_option' => $this->getOption()->getId()
            ]
        );

        $configurableOptionsBlock->addChild(
            'select',
            '\Wizkunde\ConfigurableBundle\Block\Catalog\Product\View\Options\Type\Select',
            [
                'template' => 'Wizkunde_ConfigurableBundle::product/view/options/select.phtml',
                'bundle_option' => $this->getOption()->getId()
            ]
        );

        $configurableOptionsBlock->addChild(
            'date',
            '\Wizkunde\ConfigurableBundle\Block\Catalog\Product\View\Options\Type\Date',
            [
                'template' => 'Wizkunde_ConfigurableBundle::product/view/options/date.phtml',
                'bundle_option' => $this->getOption()->getId()
            ]
        );

        return $configurableOptionsBlock;
    }

    public function getMultiConfigurableOptionsAsJson()
    {
        $selections = [];

        foreach ($this->getOption()->getSelections() as $currentSelection) {
            $configurableOptionsBlock = $this->getOptionsBlock($currentSelection);
            $productModel = $this->productRepository->getById($currentSelection->getProductId());
            $configurableOptionsBlock->setProduct($productModel);
            $configurableOptionsBlock->setOption($this->getOption());

            $options = $this->jsonDecoder->decode(
                $this->getConfigurableOptions($currentSelection, true)
            );

            foreach ($options as $optionId => $option) {
                $productOption = $this->productOptionRepository->get($productModel->getSku(), $optionId);
                $selections[$optionId] = $configurableOptionsBlock->getOptionHtml($productOption);
            }
        }

        return $this->_jsonEncoder->encode($selections);
    }

    public function getConfigurableOptionsAsJson()
    {
        foreach ($this->getOption()->getSelections() as $currentSelection) {
            if ($this->isSelected($currentSelection)) {
                $block = $this->optionsFactory->create();
                $block->setProduct($this->productRepository->getById($currentSelection->getProductId()));

                return $block->getJsonConfig();
            }
        }

        return '{}';
    }

    public function getJsonConfigurableOptions()
    {
        return $this->_jsonEncoder->encode($this->getProduct()->getCustomOptions());
    }

    public function getSelectedProduct()
    {
        foreach ($this->getOption()->getSelections() as $currentSelection) {
            if ($this->isSelected($currentSelection)) {
                return $currentSelection->getSelectionId();
            }
        }

        return 0;
    }

    public function getBuyRequest()
    {
        return '{}';
    }

    public function getSelectionOptions()
    {
        foreach ($this->getOption()->getSelections() as $currentSelection) {
            if ($this->isSelected($currentSelection)) {
                $productModel = $this->productRepository->getById($currentSelection->getProductId());
                return $productModel->getOptions();
            }
        }

        return [];
    }

    public function getMultiConfigurableOptions()
    {
        $configurableOptions = [];

        foreach ($this->getOption()->getSelections() as $selection) {
            $configurableOptions[$selection->getSelectionId()] = $this->getConfigurableOptions($selection);
        }

        return $this->_jsonEncoder->encode($configurableOptions);
    }

    public function getSuperOptions()
    {
        if ($this->isEditProduct()) {
                $options =  $this->quoteItemOption->create()->getCollection()->addFieldToFilter('item_id', $this->getRequest()->getParam('id'))->addFieldToFilter('product_id', $this->getRequest()->getParam('product_id'))->addFieldToFilter('code', 'info_buyRequest')->getFirstItem()->getData();

            $option = json_decode($options['value'], true);
            return $option;
        } else {
            if ($this->getRequest()->getModuleName()=="sales") {
                $options =  $this->quoteItemOption->create()->getCollection()->addFieldToFilter('item_id', $this->getRequest()->getParam('id'))->addFieldToFilter('product_id', $this->getOption()->getParentId())->addFieldToFilter('code', 'info_buyRequest')->getFirstItem()->getData();
                if (isset($options['value'])) {
                    $option = json_decode($options['value'], true);
                    return $option;
                }
            }
        }

        return [];
    }

    public function isEditProduct()
    {
        
        if ($this->getRequest()->getModuleName()=="checkout") {
            return true;
        } else {
            return false;
        }
    }
}
