<?php

namespace SM\Bundle\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class BundleAttribute extends AbstractHelper
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var \Magento\Swatches\Helper\Data
     */
    private $swatchHelper;
    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    private $productResource;
    /**
     * @var \Magento\Framework\Pricing\Adjustment\CalculatorInterface
     */
    private $calculator;

    /**
     * BundleAttribute constructor.
     * @param Context $context
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Swatches\Helper\Data $swatchHelper
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Catalog\Model\ResourceModel\Product $productResource
     * @param \Magento\Framework\Pricing\Adjustment\CalculatorInterface $calculator
     */
    public function __construct(
        Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Swatches\Helper\Data $swatchHelper,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Framework\Pricing\Adjustment\CalculatorInterface $calculator
    ) {
        $this->productRepository = $productRepository;
        $this->swatchHelper = $swatchHelper;
        $this->eavConfig = $eavConfig;
        $this->productResource = $productResource;
        $this->calculator = $calculator;
        parent::__construct($context);
    }

    public function getBundleAttQtyValue($sku, $attCode)
    {
        $product = $this->productRepository->get($sku);
        $optionId = $product->getData($attCode);
        $attr = $this->productResource->getAttribute($attCode);
        if ($attr->usesSource()) {
            return $attr->getSource()->getOptionText($optionId);
        }
        return $optionId;
    }

    public function getAllAttributeConfigurable($id)
    {
        $product = $this->productRepository->getById($id);
        return $product->getTypeInstance()->getConfigurableAttributes($product);

    }

    public function getBundleAttQtyValueById($id, $attCode)
    {
        $product = $this->productRepository->getById($id);
        $optionId = $product->getData($attCode);
        $attr = $this->productResource->getAttribute($attCode);
        if ($attr->usesSource()) {
            return $attr->getSource()->getOptionText($optionId);
        }
        return $optionId;
    }

    public function getOptionId($sku, $attCode)
    {
        $product = $this->productRepository->get($sku);
        return $product->getData($attCode);
    }

    public function getAttrIdByCode($attCode)
    {
        $attr = $this->productResource->getAttribute($attCode);
        return $attr->getAttributeId();
    }

    public function getSwatchColor($optionId)
    {
        $hashcodeData = $this->swatchHelper->getSwatchesByOptionsId([$optionId]);
        return array_key_exists($optionId, $hashcodeData) ? $hashcodeData[$optionId]['value'] : '#fff';
    }

    public function getSwatchColorById($id)
    {
        $product = $this->productRepository->getById($id);
        $optionId = $product->getData('color');
        $hashcodeData = $this->swatchHelper->getSwatchesByOptionsId([$optionId]);
        return array_key_exists($optionId, $hashcodeData) ? $hashcodeData[$optionId]['value'] : '#fff';
    }

    public function getListProductConfiguration($id)
    {
        $parentProduct = $this->productRepository->getById($id);
        $data = $parentProduct->getTypeInstance()->getConfigurableOptions($parentProduct);

        $options = [];
        $minSku = [];
        foreach ($data as $attr) {
            $count = 0;
            $minPrice = 0;
            foreach ($attr as $p) {
                $product = $this->productRepository->get($p['sku']);
                if ($count == 0) {
                    $minPrice = $product->getFinalPrice();
                    $minSku[$parentProduct->getSku()][$p['attribute_code']]['label'] = $p['option_title'];
                    $minSku[$parentProduct->getSku()][$p['attribute_code']]['id'] = $p['value_index'];
                    $minSku[$parentProduct->getSku()]['sku'] = $product->getSku();
                }
                if ($product->getFinalPrice() < $minPrice) {
                    $minPrice = $product->getFinalPrice();
                    $minSku[$parentProduct->getSku()][$p['attribute_code']]['label'] = $p['option_title'];
                    $minSku[$parentProduct->getSku()][$p['attribute_code']]['id'] = $p['value_index'];
                    $minSku[$parentProduct->getSku()]['sku'] = $product->getSku();
                }
                $options['item'][$p['sku']][$p['attribute_code']] = $p['option_title'];
                $options['item'][$p['sku']]['price'] = $product->getPrice();
                $options['item'][$p['sku']]['id'] = $product->getId();

                $count++;
            }

        }
        $options['min'] = $minSku;

        return $options;
    }

    public function getMinPrice($productBundle, $api = true)
    {
        $totalMinPrice = $this->getMinAmount($productBundle, $api);
        return $this->calculator->getAmount($totalMinPrice, $productBundle);
    }

    public function getRegularPrice($productBundle, $api = false)
    {
        $totalMinPrice = $this->getMinAmount($productBundle, $api, true);
        return $this->calculator->getAmount($totalMinPrice, $productBundle);
    }

    public function getMinAmount($productBundle, $api = false, $regular = false, $viewDetail = false)
    {
        $configuration = $productBundle->getExtensionAttributes()->getBundleProductOptions();
        if ($configuration == null) {
            $productBundleData = $this->productRepository->getById($productBundle->getId());
            $configuration = $productBundleData->getExtensionAttributes()->getBundleProductOptions();
        } else {
            $viewDetail = true;
        }
        $totalMinPrice = 0;
        if ($configuration) {
            foreach ($configuration as $item) {
                $minPrice = 0;
                $regularPrice = 0;
                $count = 0;
                if (is_array($item->getProductLinks()) && !empty($item->getProductLinks())) {
                    foreach ($item->getProductLinks() as $option) {
                        $sku = $option->getSku();
                        $productOp = $this->productRepository->get($sku);
                        if ($productOp->getTypeId() == 'configurable') {
                            foreach ($productOp->getExtensionAttributes()->getConfigurableProductLinks() as $itemId) {
                                $product = $this->productRepository->getById($itemId);
                                if ($count == 0) {
                                    $minPrice     = $product->getFinalPrice() * (int)$option->getQty();
                                    $regularPrice = $product->getPrice() * (int)$option->getQty();
                                }
                                if ($product->getFinalPrice() * (int)$option->getQty() < $minPrice) {
                                    $minPrice     = $product->getFinalPrice() * (int)$option->getQty();
                                    $regularPrice = $product->getPrice() * (int)$option->getQty();
                                }
                                $count++;
                            }
                        } else {
                            if ($option->getPrice() != null && $option->getPriceType() != null) {
                                if ($count == 0) {
                                    $minPrice = $option->getPrice() * (int)$option->getQty();
                                }
                                if ($option->getPrice() < $minPrice) {
                                    $minPrice = $option->getPrice() * (int)$option->getQty();
                                }
                            } else {
                                if ($count == 0) {
                                    $minPrice     = $productOp->getFinalPrice() * (int)$option->getQty();
                                    $regularPrice = $productOp->getPrice() * (int)$option->getQty();
                                }
                                if ($productOp->getFinalPrice() * (int)$option->getQty() < $minPrice) {
                                    $minPrice     = $productOp->getFinalPrice() * (int)$option->getQty();
                                    $regularPrice = $productOp->getPrice() * (int)$option->getQty();
                                }
                            }
                            $count++;
                        }
                    }
                } else {
                    $productOp = $this->productRepository->get($item->getSku());
                    if ($productOp->getTypeId() == 'configurable') {
                        foreach ($productOp->getExtensionAttributes()->getConfigurableProductLinks() as $itemId) {
                            $product = $this->productRepository->getById($itemId);
                            if ($count == 0) {
                                $minPrice     = $product->getFinalPrice() * (int)$itemId->getQty();
                                $regularPrice = $product->getPrice() * (int)$itemId->getQty();
                            }
                            if ( $product->getFinalPrice() * (int)$itemId->getQty() < $minPrice) {
                                $minPrice     = $product->getFinalPrice() * (int)$itemId->getQty();
                                $regularPrice = $product->getPrice() * (int)$itemId->getQty();
                            }
                            $count++;
                        }
                    } else {
                        if ($count == 0) {
                            $minPrice     = $productOp->getFinalPrice();
                            $regularPrice = $productOp->getPrice();
                        }
                        if ( $productOp->getFinalPrice() < $minPrice) {
                            $minPrice     = $productOp->getFinalPrice();
                            $regularPrice = $productOp->getPrice();
                        }

                        $count++;
                    }
                }
                $totalMinPrice += $regular ? $regularPrice : $minPrice;
            }
        }

        return ($viewDetail && !$api && $regular) ? 0 : $totalMinPrice;
    }


}
