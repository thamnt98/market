<?php

declare(strict_types=1);

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
     * Configurable constructor.
     * @param \Magento\Catalog\Api\ProductAttributeRepositoryInterface $productAttributeRepository
     * @param \Magento\Framework\Json\DecoderInterface $jsonDecoder
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     */
    public function __construct(
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $productAttributeRepository,
        \Magento\Framework\Json\DecoderInterface $jsonDecoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder
    ) {
        $this->productAttributeRepository = $productAttributeRepository;
        $this->jsonDecoder = $jsonDecoder;
        $this->jsonEncoder = $jsonEncoder;
    }

    /**
     * @param \Magento\Swatches\Block\Product\Renderer\Configurable $subject
     * @param $result
     * @return mixed
     */
    public function afterGetJsonSwatchConfig(
        \Magento\Swatches\Block\Product\Renderer\Configurable $subject,
        $result
    ) {
        try {
            $attribute = $this->productAttributeRepository->get('color');
        } catch (\Exception $e) {
            return $result;
        }
        $colorId = $attribute->getAttributeId();
        $result = $this->jsonDecoder->decode($result);
        if (isset($result[$colorId])) {
            foreach ($result[$colorId] as $optionId => $optionData) {
                if (is_array($optionData) && array_key_exists('value', $optionData) && !(bool)$optionData['value']) {
                    $result[$colorId][$optionId]['value'] = $result[$colorId][$optionId]['label'];
                }
            }
        }
        return $this->jsonEncoder->encode($result);
    }
}
