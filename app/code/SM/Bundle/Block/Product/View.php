<?php

namespace SM\Bundle\Block\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;

class View extends \Magento\Catalog\Block\Product\View
{
    /**
     * @var \SM\Bundle\Helper\BundleAttribute
     */
    private $helper;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Session $customerSession,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \SM\Bundle\Helper\BundleAttribute $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $urlEncoder, $jsonEncoder, $string, $productHelper, $productTypeConfig,
            $localeFormat, $customerSession, $productRepository, $priceCurrency, $data);
    }

    public function getJsonConfig()
    {
        /* @var $product \Magento\Catalog\Model\Product */
        $product = $this->getProduct();

        if ($product->getTypeId() == 'bundle') {
            if (!$this->hasOptions()) {
                $config = [
                    'productId' => $product->getId(),
                    'priceFormat' => $this->_localeFormat->getPriceFormat()
                ];
                return $this->_jsonEncoder->encode($config);
            }

            $tierPrices = [];
            $priceInfo = $product->getPriceInfo();
            $tierPricesList = $priceInfo->getPrice('tier_price')->getTierPriceList();
            foreach ($tierPricesList as $tierPrice) {
                $tierPrices[] = $tierPrice['price']->getValue() * 1;
            }
            $config = [
                'productId'   => (int)$product->getId(),
                'priceFormat' => $this->_localeFormat->getPriceFormat(),
                'prices'      => [
                    'oldPrice'   => [
                        'amount'      => $this->helper->getMinAmount($product, false, true) * 1,
                        'adjustments' => []
                    ],
                    'basePrice'  => [
                        'amount'      => $this->helper->getMinAmount($product) * 1,
                        'adjustments' => []
                    ],
                    'finalPrice' => [
                        'amount'      => $this->helper->getMinAmount($product) * 1,
                        'adjustments' => []
                    ]
                ],
                'idSuffix'    => '_clone',
                'tierPrices'  => $tierPrices
            ];
        } else {
            if (!$this->hasOptions()) {
                $config = [
                    'productId' => $product->getId(),
                    'priceFormat' => $this->_localeFormat->getPriceFormat()
                ];
                return $this->_jsonEncoder->encode($config);
            }

            $tierPrices = [];
            $priceInfo = $product->getPriceInfo();
            $tierPricesList = $priceInfo->getPrice('tier_price')->getTierPriceList();
            foreach ($tierPricesList as $tierPrice) {
                $tierPrices[] = $tierPrice['price']->getValue() * 1;
            }
            $config = [
                'productId'   => (int)$product->getId(),
                'priceFormat' => $this->_localeFormat->getPriceFormat(),
                'prices'      => [
                    'oldPrice'   => [
                        'amount'      => $priceInfo->getPrice('regular_price')->getAmount()->getValue() * 1,
                        'adjustments' => []
                    ],
                    'basePrice'  => [
                        'amount'      => $priceInfo->getPrice('final_price')->getAmount()->getBaseAmount() * 1,
                        'adjustments' => []
                    ],
                    'finalPrice' => [
                        'amount'      => $priceInfo->getPrice('final_price')->getAmount()->getValue() * 1,
                        'adjustments' => []
                    ]
                ],
                'idSuffix'    => '_clone',
                'tierPrices'  => $tierPrices
            ];
        }


        $responseObject = new \Magento\Framework\DataObject();
        $this->_eventManager->dispatch('catalog_product_view_config', ['response_object' => $responseObject]);
        if (is_array($responseObject->getAdditionalOptions())) {
            foreach ($responseObject->getAdditionalOptions() as $option => $value) {
                $config[$option] = $value;
            }
        }

        return $this->_jsonEncoder->encode($config);
    }
}
