<?php
namespace SM\Bundle\Model;

use Magento\Quote\Model\Quote\Item\CartItemProcessorInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Bundle\Api\Data\BundleOptionInterfaceFactory;
use Magento\Quote\Api\Data as QuoteApi;


class CartItemProcessor extends \Magento\Bundle\Model\CartItemProcessor
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
        \Magento\Framework\DataObject\Factory $objectFactory,
        QuoteApi\ProductOptionExtensionFactory $productOptionExtensionFactory,
        BundleOptionInterfaceFactory $bundleOptionFactory,
        QuoteApi\ProductOptionInterfaceFactory $productOptionFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        $this->request = $request;
        $this->productRepository = $productRepository;
        parent::__construct($objectFactory, $productOptionExtensionFactory, $bundleOptionFactory,
            $productOptionFactory);
    }

    public function convertToBuyRequest(CartItemInterface $cartItem) {
        if ($cartItem->getProductOption() && $cartItem->getProductOption()->getExtensionAttributes()) {
            $options = $cartItem->getProductOption()->getExtensionAttributes()->getBundleOptions();
            if (is_array($options)) {
                $requestData = [];
                foreach ($options as $option) {
                    /** @var \Magento\Bundle\Api\Data\BundleOptionInterface $option */
                    foreach ($option->getOptionSelections() as $selection) {
                        $requestData['bundle_option'][$option->getOptionId()][] = $selection;
                        $requestData['bundle_option_qty'][$option->getOptionId()] = $option->getOptionQty();
                        if ($option->getExtensionAttributes()->getCustomSuperAttribute()) {
                            $customData = [];
                            foreach ($option->getExtensionAttributes()->getCustomSuperAttribute() as $customOption) {
                                $customData[$customOption->getOptionId()] = $customOption->getOptionValue();
                            }
                            $requestData['super_attribute'][$option->getOptionId()] = array($selection => $customData);
                        }

                    }
                }
                return $this->objectFactory->create($requestData);
            }
        }
        if ($this->request->getParam('itemId', null) != null) {
            $options = $cartItem->getQtyOptions();
            $requestData = [];
            $count = 0;
            $optionIds = array_keys($cartItem->getBuyRequest()->getBundleOptionQty());
            foreach ($options as $parentId => $item) {
                $parentProd = $this->productRepository->getById($parentId);
                if ($parentProd->getTypeId() == 'configurable') {
                    $productTypeInstance = $cartItem->getProduct()->getTypeInstance();
                    $productOption = $productTypeInstance->getSelectionsCollection($productTypeInstance->getOptionsIds($cartItem->getProduct()), $cartItem->getProduct())->getItems();
                    $option=$cartItem->getProduct()->getTypeInstance()->getOrderOptions($cartItem->getProduct());
                    $superAttribute = $this->getSelectedProduct($productOption, $option);
                    if (!empty($superAttribute)) {
                        $requestData['super_attribute'] = $superAttribute;
                    }
                }
                $requestData['bundle_option'][$optionIds[$count]] = $cartItem->getBuyRequest()->getBundleOption()[$optionIds[$count]];
                $requestData['bundle_option_qty'][$optionIds[$count]] = $cartItem->getBuyRequest()->getBundleOptionQty()[$optionIds[$count]];

                $count++;
            }
            return $this->objectFactory->create($requestData);
        }
        return null;
    }

    public function getSelectedProduct($productOption, $option)
    {
        $products = [];
        $infoBuyRequest = $option['info_buyRequest'];
        $bundleOption = $infoBuyRequest['bundle_option'];
        $supperAttributes = $infoBuyRequest['super_attribute'];
        $optionSelected = [];
        foreach ($productOption as $optionId=>$productOpt) {
            foreach ($bundleOption as $opt) {
                if ($optionId==$opt) {
                    $optionSelected[] = $productOpt;
                    if ($productOpt->getTypeId()=='configurable') {
                        $attrOpt=0;
                        foreach ($bundleOption as $k=>$v) {
                            if ($v==$optionId) {
                                $attrOpt = $k;
                            }
                        }
                        $attributes = $productOpt->getTypeInstance()->getConfigurableAttributesAsArray($productOpt);
                        $attributeId = head(array_keys($supperAttributes[$attrOpt][$optionId]));
                        $attributeSelected=head(array_values($supperAttributes[$attrOpt][$optionId]));
                        $attribute_code = $attributes[$attributeId]['attribute_code'];
                        $usedProduct=$productOpt->getTypeInstance()->getUsedProducts($productOpt);
                        foreach ($usedProduct as $product) {
                            if ($product->getData($attribute_code) == $attributeSelected) {
                                $products[$attrOpt][$optionId][$attributeId] = $attributeSelected;
                            }
                        }
                    }
                }
            }
        }

        return $products;
    }
}
