<?php

namespace Wizkunde\ConfigurableBundle\Observer;

class AdjustJsonConfigObserver implements \Magento\Framework\Event\ObserverInterface
{
    protected $registry;
    protected $productRepository = null;
    protected $configurableType = null;
    protected $simpleType = null;
    protected $jsonDecoder = null;

    protected $product = null;
    protected $config = null;

    /**
     * AfterBundleJson constructor.
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $configurableType
     * @param \Magento\Catalog\Block\Product\View\Type\Simple $simpleType
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Json\DecoderInterface $jsonDecoder
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $configurableType,
        \Magento\Catalog\Block\Product\View\Type\Simple $simpleType,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Json\DecoderInterface $jsonDecoder
    ) {
        $this->registry = $registry;

        $this->simpleType = $simpleType;
        $this->configurableType = $configurableType;
        $this->productRepository = $productRepository;
        $this->jsonDecoder = $jsonDecoder;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $this->product = $this->registry->registry('current_product');

        if ($this->product->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
            $priceConfigObj = $observer->getData('configObj');
            $this->config = $priceConfigObj->getConfig();

            foreach ($this->product->getOptions() as $option) {
                $this->addConfigurableSelection($option);
            }

            $priceConfigObj->setConfig($this->config);
        }
    }

    /**
     * Add the configurable selections to the option
     */
    private function addConfigurableSelection($option)
    {
        if(!is_array($option->getSelections())) {
            return;
        }

        foreach ($option->getSelections() as $selection) {
            $fullProduct = $this->productRepository->getById($selection->getId());
            $this->config['options'][$option->getOptionId()]['selections'][$selection->getSelectionId()]['description'] = $fullProduct->getShortDescription();

            if ($selection->getTypeId() == 'configurable') {
                if (isset($this->config['options'
                    ][$option->getOptionId()])) {
                    $optionData = $this->config['options'][$option->getOptionId()];

                    if (isset($optionData['selections'][$selection->getSelectionId()])) {
                        $newSelection = array_merge_recursive(
                            $this->config['options'][$option->getOptionId()]['selections'][$selection->getSelectionId()],
                            $this->getConfigurableInfo($fullProduct)
                        );
                    }
                }
            } else {
                $simpleProduct = $this->simpleType->setProduct($fullProduct);
                $simpleJson = json_decode($simpleProduct->getJsonConfig());

                if (is_array($simpleJson)) {
                    $newSelection = array_merge_recursive(
                        $this->config['options'][$option->getOptionId()]['selections'][$selection->getSelectionId()],
                        $this->jsonDecoder->decode($simpleProduct->getJsonConfig())
                    );
                }
            }

            if (isset($newSelection)) {
                $this->config['options'][$option->getOptionId()]['selections'][$selection->getSelectionId()]
                    = $newSelection;
            }
        }
    }

    /**
     * Get the Configrable Information
     */
    private function getConfigurableInfo($selection)
    {
        $confData = [];

        $this->configurableType->setProduct($selection);

        $confData['confProductId'] = $selection->getId();
        $jsConf = $this->configurableType->getJsonConfig();
        $jsConf = str_replace('attributes', 'confAttributes', $jsConf);
        $confData['configurableOptions'][$selection->getId() . '_' . $selection->getId()]= $this->jsonDecoder->decode($jsConf);
        return $confData;
    }
}