<?php


namespace SM\Catalog\Ui\DataProvider\Product\Listing\Collector;


use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductRender\PriceInfoInterface;
use Magento\Catalog\Api\Data\ProductRender\PriceInfoInterfaceFactory;
use Magento\Catalog\Api\Data\ProductRenderInterface;
use Magento\Catalog\Model\ProductRender\FormattedPriceInfoBuilder;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\GroupedProduct\Model\Product\Type\Grouped;

class Price extends \Magento\Catalog\Ui\DataProvider\Product\Listing\Collector\Price
{
    /**
     * @var PriceInfoInterfaceFactory
     */
    protected $priceInfoFactory;
    /**
     * @var FormattedPriceInfoBuilder
     */
    protected $formattedPriceInfoBuilder;
    /**
     * @var \SM\Catalog\Helper\Data
     */
    protected $dataHelper;

    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        PriceInfoInterfaceFactory $priceInfoFactory,
        FormattedPriceInfoBuilder $formattedPriceInfoBuilder,
        \SM\Catalog\Helper\Data $dataHelper,
        array $excludeAdjustments = []
    ) {
        parent::__construct($priceCurrency, $priceInfoFactory, $formattedPriceInfoBuilder, $excludeAdjustments);
        $this->priceInfoFactory = $priceInfoFactory;
        $this->formattedPriceInfoBuilder = $formattedPriceInfoBuilder;
        $this->dataHelper = $dataHelper;
    }
    /**
     * @inheritdoc
     */
    public function collect(ProductInterface $product, ProductRenderInterface $productRender)
    {
        $priceInfo = $productRender->getPriceInfo();

        if (!$productRender->getPriceInfo()) {
            /** @var PriceInfoInterface $priceInfo */
            $priceInfo = $this->priceInfoFactory->create();
        }
        if ($product->getTypeId()==Grouped::TYPE_CODE) {
            $product = $this->dataHelper->getMinProduct($product);
        }

        if ($product->getTypeId() != 'bundle') {
            $priceInfo->setFinalPrice(
                $product
                    ->getPriceInfo()
                    ->getPrice('final_price')
                    ->getAmount()
                    ->getValue()
            );
            $priceInfo->setMinimalPrice(
                $product
                    ->getPriceInfo()
                    ->getPrice('final_price')
                    ->getMinimalPrice()
                    ->getValue()
            );
            $priceInfo->setRegularPrice(
                $product
                    ->getPriceInfo()
                    ->getPrice('regular_price')
                    ->getAmount()
                    ->getValue()
            );
            $priceInfo->setMaxPrice(
                $product
                    ->getPriceInfo()
                    ->getPrice('final_price')
                    ->getMaximalPrice()
                    ->getValue()
            );
        } else {
            $minPrice = $this->dataHelper->getSumPriceMinBundle($product);
            $priceInfo->setFinalPrice(
                $minPrice['special']
            );
            $priceInfo->setMinimalPrice(
                $minPrice['special']
            );
            $priceInfo->setRegularPrice(
                $minPrice['regular']
            );
            $priceInfo->setMaxPrice(
                $minPrice['regular']
            );
        }

        $this->formattedPriceInfoBuilder->build(
            $priceInfo,
            $productRender->getStoreId(),
            $productRender->getCurrencyCode()
        );
        $productRender->setPriceInfo($priceInfo);
    }
}
