<?php

namespace Wizkunde\ConfigurableBundle\Block\Product\View\Type;

class Grouped extends \Magento\GroupedProduct\Block\Product\View\Type\Grouped
{
    private $jsonEncoder = null;
    private $jsonDecoder = null;
    private $productRepository = null;
    private $productOptionRepository = null;
    private $imageFactory = null;
    private $configurableRenderer = null;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Stdlib\ArrayUtils $arrayUtils
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Json\DecoderInterface $jsonDecoder,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Model\Product\Option\Repository $productOptionRepository,
        \Wizkunde\ConfigurableBundle\Block\Catalog\Product\Renderer\Configurable $configurableRenderer,
        \Magento\Catalog\Helper\ImageFactory $imageFactory,
        array $data = []
    )
    {
        $this->arrayUtils = $arrayUtils;
        parent::__construct(
            $context,
            $arrayUtils
        );

        $this->jsonDecoder = $jsonDecoder;
        $this->jsonEncoder = $jsonEncoder;
        $this->productRepository = $productRepository;
        $this->productOptionRepository = $productOptionRepository;
        $this->configurableRenderer = $configurableRenderer;
        $this->imageFactory = $imageFactory;
    }

    /**
     * @return string
     */
    public function getJsonSwatchConfig($item)
    {
        if ($item->getTypeId() == 'configurable') {
            $configurableProduct = $this->productRepository->getById($item->getId());

            $configurableRenderer = clone($this->configurableRenderer);
            $configurableRenderer->setProduct($configurableProduct);
            return $configurableRenderer->getJsonSwatchConfig();
        }

        return '{}';
    }

    public function getConfigurableOptionsAsJson($item)
    {
        $optionhtml = array();


        $magentoDefault_options_type = array('field', 'area', 'file', 'drop_down', 'radio', 'checkbox', 'multiple', 'date', 'date_time', 'time');

        $configurableOptionsBlock = $this->getOptionsBlock($item);
        $productModel = $this->productRepository->getById($item->getId());
        $configurableOptionsBlock->setProduct($productModel);
        $configurableOptionsBlock->setOption($this->getOption());

        $options = $this->jsonDecoder->decode(
            $this->getConfigurableOptions($item, true)
        );

        foreach ($options as $optionId => $option) {
            $productOption = $this->productOptionRepository->get($productModel->getSku(), $optionId);
            /* check if the magento default options type exists*/
            if (in_array($option['type'], $magentoDefault_options_type)) {
                $optionhtml[$optionId] = $configurableOptionsBlock->getOptionHtml($productOption);
            }
        }

        return $this->jsonEncoder->encode($optionhtml);
    }

    public function getConfigurableOptions($item, $serialize = false)
    {
        $configurableOptionsBlock = $this->getOptionsBlock($item);

        $magentoDefault_options_type = array('field', 'area', 'file', 'drop_down', 'radio', 'checkbox', 'multiple', 'date', 'date_time', 'time');

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $productModel = $objectManager->create('\Magento\Catalog\Model\Product')->load($item->getId());

        $configurableOptionsBlock->setProduct($productModel);

        if ($serialize == false) {
            $optionHtml = '';

            foreach ($this->productOptionRepository->getProductOptions($productModel) as $_option) {
                /* check if the magento default options type exists*/
                if (in_array($_option->getType(), $magentoDefault_options_type)) {

                    $optionHtml .= $configurableOptionsBlock->getOptionHtml($_option);

                }

            }

            return $optionHtml;
        } else {
            return $configurableOptionsBlock->getJsonConfig();
        }
    }

    public function getMultiConfigurableOptions()
    {
        $configurableOptions = [];

        foreach ($this->getOption()->getSelections() as $selection) {
            $configurableOptions[$selection->getSelectionId()] = $this->getConfigurableOptions($selection);
        }

        return $this->jsonEncoder->encode($configurableOptions);
    }

    /**
     * @return string
     */
    public function getJsonConfig($item)
    {
        if ($item->getTypeId() == 'configurable') {
            $configurableProduct = $this->productRepository->getById($item->getId());

            $configurableRenderer = clone($this->configurableRenderer);
            $configurableRenderer->setProduct($configurableProduct);

            return $configurableRenderer->getJsonConfig();
        }

        return '{}';
    }

    /**
     * @return string
     */
    public function getGridConfig($item)
    {
        if ($item->getTypeId() == 'configurable') {
            $configurableProduct = $this->productRepository->getById($item->getId());

            $configurableRenderer = clone($this->configurableRenderer);
            $configurableRenderer->setProduct($configurableProduct);

            if ($configurableProduct->getShowAsGrid()) {
                $returnData = json_decode($configurableRenderer->getJsonConfig(), true);

                if (isset($returnData['attributes']) && count($returnData['attributes']) > 2) {
                    $returnData['attributes'] = array_slice($returnData['attributes'], -2, 2, true);
                }

                return $returnData;
            }
        }

        return array();
    }

    /**
     * @return string
     */
    public function getJsonConfigForSwatches($item)
    {
        if ($item->getTypeId() == 'configurable') {
            $configurableProduct = $this->productRepository->getById($item->getId());

            $configurableRenderer = clone($this->configurableRenderer);
            $configurableRenderer->setProduct($configurableProduct);

            if ($configurableProduct->getShowAsGrid()) {
                $jsonConfig = json_decode($configurableRenderer->getJsonConfig(), true);

                if (isset($jsonConfig['attributes']) && count($jsonConfig['attributes']) > 2) {
                    // Make swatches out of everything but the last 2 items
                    $jsonConfig['attributes'] = array_slice($jsonConfig['attributes'], 0, -2, true);
                }

                return json_encode($jsonConfig);
            }

            return $configurableRenderer->getJsonConfig();
        }

        return '{}';
    }

    protected function getOptionsBlock($selection = null)
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
                'template' => 'Wizkunde_ConfigurableBundle::product/view/options/default.phtml'
            ]
        );

        $configurableOptionsBlock->addChild(
            'text',
            '\Wizkunde\ConfigurableBundle\Block\Catalog\Product\View\Options\Type\Text',
            [
                'template' => 'Wizkunde_ConfigurableBundle::product/view/options/text.phtml'
            ]
        );

        $configurableOptionsBlock->addChild(
            'file',
            '\Wizkunde\ConfigurableBundle\Block\Catalog\Product\View\Options\Type\File',
            [
                'template' => 'Wizkunde_ConfigurableBundle::product/view/options/file.phtml'
            ]
        );

        $configurableOptionsBlock->addChild(
            'select',
            '\Wizkunde\ConfigurableBundle\Block\Catalog\Product\View\Options\Type\Select',
            [
                'template' => 'Wizkunde_ConfigurableBundle::product/view/options/select.phtml'
            ]
        );

        $configurableOptionsBlock->addChild(
            'date',
            '\Wizkunde\ConfigurableBundle\Block\Catalog\Product\View\Options\Type\Date',
            [
                'template' => 'Wizkunde_ConfigurableBundle::product/view/options/date.phtml'
            ]
        );

        return $configurableOptionsBlock;
    }

    public function getProductThumbnail($product)
    {
        $fullProduct = $this->productRepository->getById($product->getId());

        return $this->imageFactory->create()->init(
            $fullProduct,
            'product_small_image',
            array(
                'width' => 100,
                'height' => 100
            )
        )->getUrl();
    }

}
