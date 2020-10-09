<?php


namespace SM\CustomPrice\Observer\Quote;


use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\Quote\ItemFactory;
use SM\Bundle\Helper\BundleAttribute;

class AfterAddToCart implements ObserverInterface
{
    /**
     * @var Product
     */
    private $_productCollection;
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $_customerSession;
    /**
     * @var BundleAttribute
     */
    private $bundleAttribute;
    /**
     * @var ItemFactory
     */
    private $itemFactory;
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    public function __construct(
        Product $product,
        \Magento\Customer\Model\Session $customerSession,
        BundleAttribute $bundleAttribute,
        ItemFactory $itemFactory,
    ProductRepository $productRepository
    ) {
        $this->_productCollection = $product;
        $this->_customerSession   = $customerSession;
        $this->bundleAttribute = $bundleAttribute;
        $this->itemFactory = $itemFactory;
        $this->productRepository = $productRepository;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer      = $this->_customerSession;
        $attributeCode = $customer->getOmniNormalPriceAttributeCode();
        $item          = $observer->getEvent()->getData('quote_item');
        /** @var Item $item */
        $item          = ($item->getParentItem() ? $item->getParentItem() : $item);
        $product       =$this->productRepository->getById($item->getProductId());

        if ($product->getTypeId() == Configurable::TYPE_CODE) {
            $option=$item->getProduct()->getTypeInstance()->getOrderOptions($item->getProduct());
            $product = $this->productRepository->get($option['simple_sku']);
        }
        $price         = $product->getData($attributeCode);
        if ($product->getTypeId() == 'bundle') {
            $productTypeInstance = $item->getProduct()->getTypeInstance();
            $productOption = $productTypeInstance->getSelectionsCollection($productTypeInstance->getOptionsIds($item->getProduct()),
                $item->getProduct())->getItems();
            $option=$item->getProduct()->getTypeInstance()->getOrderOptions($item->getProduct());
            $selectedProduct = $this->getSelectedProduct($productOption, $option);
            $price = 0;
            foreach ($selectedProduct as $p) {
                $price+=$p->getPrice();
            }
        }
        try {
            $item->setBasePriceByLocation($price);
            $item->save();
            $quote         = $item->getQuote();
            if (!$quote->getOmniStoreId()) {
                $omni_store_id = $customer->getOmniStoreId();
                $quote->setOmniStoreId($omni_store_id);
                $quote->save();
            }
        } catch (\Exception $exception) {

        }
    }
/*
 * Get selected product for bundle
 *
 */
    public function getSelectedProduct($productOption,$option)
    {
        $products = [];
        $optionSelected = [];
        $infoBuyRequest = $option['info_buyRequest'];
        $bundleOption = $infoBuyRequest['bundle_option'];
        foreach ($productOption as $optionId=>$productOpt) {
            foreach ($bundleOption as $opt) {
                if ($optionId==$opt) {
                    $optionSelected[] = $productOpt;
                    if ($productOpt->getTypeId()==Configurable::TYPE_CODE) {
                        if (!empty($infoBuyRequest['super_attribute'])) {
                            $supperAttributes = $infoBuyRequest['super_attribute'];
                            $attrOpt          = 0;
                            foreach ($bundleOption as $k => $v) {
                                if ($v == $optionId) {
                                    $attrOpt = $k;
                                }
                            }
                            $attributes        = $productOpt->getTypeInstance()->getConfigurableAttributesAsArray($productOpt);
                            $attributeId       = head(array_keys($supperAttributes[$attrOpt][$optionId]));
                            $attributeSelected = head(array_values($supperAttributes[$attrOpt][$optionId]));
                            $attrbute_code     = $attributes[$attributeId]['attribute_code'];
                            $usedProduct       = $productOpt->getTypeInstance()->getUsedProducts($productOpt);
                            /** @var Product $product */
                            foreach ($usedProduct as $product) {
                                if ($product->getData($attrbute_code) == $attributeSelected) {
                                    $products[] = $product;
                                }
                            }
                        }

                    } else {
                        $products[] = $productOpt;
                    }

                }
            }
        }

        return $products;
    }


}
