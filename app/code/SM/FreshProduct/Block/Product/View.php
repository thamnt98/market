<?php

namespace SM\FreshProduct\Block\Product;

use SM\FreshProduct\Helper\Data as FreshHelper;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;

class View extends \Magento\Framework\View\Element\Template
{

    const KGS = 'kgs';
    const LBS = 'lbs';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var PriceHelper
     */
    protected $priceHelper;

    /**
     * @var FreshHelper
     */
    protected $freshHelper;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product = null;

    public function __construct(
        FreshHelper $freshHelper,
        PriceHelper $priceHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_registry = $registry;
        $this->priceHelper = $priceHelper;
        $this->freshHelper =$freshHelper;
        $this->productRepository = $productRepository;
    }

    /**
     * @return \Magento\Catalog\Model\Product|null
     */
    public function getProduct()
    {
        if ($this->product === null) {
            if ($this->getData('product')) {
                $this->product = $this->getData('product');
            } elseif ($this->_registry->registry('product')) {
                $this->product = $this->_registry->registry('product');
            }
        }

        return $this->product;
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     *
     * @return $this
     */
    public function setProduct($product)
    {
        try {
            $this->product = $this->productRepository->getById($product->getId());
        } catch (\Exception $e) {
        }

        return $this;
    }

    public function isFreshProduct($product)
    {
        return $this->freshHelper->isFreshProduct($product);
    }

    public function isPriceInKg($product)
    {
        return $this->freshHelper->isPriceInKg($product);
    }

    public function getProductWeight($product)
    {
        if ($weight = $product->getWeight()) {
            switch ($this->getWeightUnit()) {
                case self::KGS:
                    $value = $weight * 1000;
                    break;
                case self::LBS:
                    $value = $weight * 453.5;
                    break;
                default:
                    $value = $weight;
            }
            return __('Estimated weight: ~%1 gram', (float)$value);
        }
        return '';
    }

    public function getPricePerKg($product)
    {
        return $this->freshHelper->getPricePerKg($product);
    }

    public function getSoldIn($product)
    {
        return $this->freshHelper::PACK .' '. $this->freshHelper->getSoldIn($product);
    }

    public function getWeightUnit()
    {
        return $this->_scopeConfig->getValue(
            'general/locale/weight_unit',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
