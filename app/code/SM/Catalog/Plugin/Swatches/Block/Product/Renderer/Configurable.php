<?php
declare(strict_types = 1);

namespace SM\Catalog\Plugin\Swatches\Block\Product\Renderer;

class Configurable
{
    /**
     * @var \Magento\Catalog\Api\ProductAttributeRepositoryInterface
     */
    protected $productAttributeRepository;

    /**
     * @var \Magento\Framework\Json\DecoderInterface
     */
    protected $jsonDecoder;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Configurable constructor.
     *
     * @param \Magento\Catalog\Api\ProductRepositoryInterface          $productRepository
     * @param \Magento\Catalog\Api\ProductAttributeRepositoryInterface $productAttributeRepository
     * @param \Magento\Framework\Json\DecoderInterface                 $jsonDecoder
     * @param \Magento\Framework\Json\EncoderInterface                 $jsonEncoder
     */
    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $productAttributeRepository,
        \Magento\Framework\Json\DecoderInterface $jsonDecoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder
    ) {
        $this->productAttributeRepository = $productAttributeRepository;
        $this->jsonDecoder                = $jsonDecoder;
        $this->jsonEncoder                = $jsonEncoder;
        $this->productRepository          = $productRepository;
    }

    /**
     * @param \Magento\Swatches\Block\Product\Renderer\Configurable $subject
     * @param string                                                $result
     *
     * @return string
     */
    public function afterGetJsonSwatchConfig(
        \Magento\Swatches\Block\Product\Renderer\Configurable $subject,
        $result
    ) {
        $result = $this->jsonDecoder->decode($result);

        $this->addColor($result);

        return $this->jsonEncoder->encode($result);
    }

    /**
     * @param array $result
     */
    protected function addColor(&$result)
    {
        try {
            $attribute = $this->productAttributeRepository->get('color');
        } catch (\Exception $e) {
            return;
        }

        $colorId = $attribute->getAttributeId();

        if (isset($result[$colorId])) {
            foreach ($result[$colorId] as $optionId => $optionData) {
                if (is_array($optionData) && array_key_exists('value', $optionData) && !(bool)$optionData['value']) {
                    $result[$colorId][$optionId]['value'] = $result[$colorId][$optionId]['label'];
                }
            }
        }
    }

    /**
     * @param \Magento\Swatches\Block\Product\Renderer\Configurable $subject
     * @param string                                                $result
     *
     * @return string
     */
    public function afterGetJsonConfig(\Magento\Swatches\Block\Product\Renderer\Configurable $subject, $result)
    {
        $result = $this->jsonDecoder->decode($result);

        $this->addDetails($subject, $result);

        return $this->jsonEncoder->encode($result);
    }

    /**
     * @param \Magento\Swatches\Block\Product\Renderer\Configurable $subject
     * @param array                                                 $result
     */
    protected function addDetails($subject, &$result)
    {
        /** @var \Magento\Catalog\Model\Product[] $products */
        $products = $subject->getAllowProducts();
        $parent   = null;
        try {
            $layout     = $subject->getLayout();
            $blockGroup = $this->getDetailsBlockName($subject);
        } catch (\Exception $e) {
            return;
        }

        foreach ($blockGroup as $name) {
            $block = $layout->getBlock($name);
            if (!$block || !$block->getData('is_dynamic')) {
                continue;
            }

            $alias = $layout->getElementAlias($name);
            if (!$parent) {
                $parent = $block->getProduct();
            }

            if ($parent->getTypeId() !== \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                return;
            }

            $products[] = $parent;
            $this->addChildDetail($result, $products, $alias, $block);
        }
    }

    /**
     * @param array                                         $result
     * @param \Magento\Catalog\Model\Product[]              $products
     * @param string                                        $key
     * @param \Magento\Framework\View\Element\AbstractBlock $block
     */
    protected function addChildDetail(&$result, $products, $key, $block)
    {
        foreach ($products as $product) {
            if (!$product || !$product->getId()) {
                continue;
            }

            try {
                $product = $this->productRepository->getById($product->getId());
            } catch (\Exception $e) {
                continue;
            }

            if ($product->getTypeId() !== \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                $id = $product->getId();
            } else {
                $id = 0;
            }

            $block->setData('product', $product);
            $result['details'][$id][$key] = $block->toHtml();
        }

        $block->setData('product');
    }

    /**
     * Get Block details group.
     *
     * @param \Magento\Swatches\Block\Product\Renderer\Configurable $block
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getDetailsBlockName($block)
    {
        $detailBlock = $block->getLayout()->getBlock('product.info.details');

        return $detailBlock->getGroupSortedChildNames('detailed_info', 'getChildHtml');
    }
}
