<?php

namespace SM\Bundle\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Model\Product\Type;

class SetPriceForItem implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        $this->request = $request;
        $this->productRepository = $productRepository;
    }

    public function execute(Observer $observer)
    {
        $bundleItem = null;
        /** @var $item \Magento\Quote\Model\Quote\Item */
        $item = $observer->getEvent()->getQuoteItem();
        if ($item->getProduct()->getTypeId() == Type::TYPE_BUNDLE) {
            foreach ($item->getQuote()->getAllItems() as $bundleitems) {
                /** @var $bundleitems\Magento\Quote\Model\Quote\Item */
                //Skip the bundle product
                if ($bundleitems->getProduct()->getTypeId() == Type::TYPE_BUNDLE) {
                    $bundleItem = $bundleitems;
                    continue;
                }
                if ($bundleitems->getProduct()->getTypeId() == 'configurable') {
                    $bundleItemSku = '';
                    $buyRequest = $this->request->getParams();
                    $quantity = $this->request->getParam('bundle_option_qty');
                    $subProduct = $bundleitems->getProduct();
                    $children = $subProduct->getTypeInstance()->getUsedProducts($subProduct);
                    $attributesSubProduct = $subProduct->getTypeInstance()->getConfigurableAttributes($subProduct);
                    foreach ($buyRequest['super_attribute'] as $attributeKey => $subAttribute) {
                        foreach ($subAttribute as $subAttributeKey => $child) {
                            if (isset($quantity[$attributeKey]) && (int)$quantity[$attributeKey] > 0) {
                                foreach ($children as $childProd) {
                                    foreach ($attributesSubProduct as $attr) {
                                        $attrId = $attr->getAttributeId();
                                        if (isset($child[$attrId])) {
                                            $attrCode = $attr->getProductAttribute()->getAttributeCode();
                                            if ($childProd->getData($attrCode) == $child[$attrId]) {
                                                $price = $childProd->getFinalPrice();
                                                $bundleItemSku = $childProd->getSku();
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($bundleitems->getSku() == $bundleItemSku) {
                        $bundleitems->setCustomPrice($price);
                        $bundleitems->setOriginalCustomPrice($price);
                        $bundleitems->getProduct()->setIsSuperMode(true);
                    }
                }
            }
            $item->getProduct()->setIsSuperMode(true);
        }
        return $this;
    }
}
