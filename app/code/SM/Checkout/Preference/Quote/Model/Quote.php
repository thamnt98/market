<?php

namespace SM\Checkout\Preference\Quote\Model;

/*
 * Fix add quote item with group configurable product and bundle configurable product
 */

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\GroupedProduct\Model\Product\Type\Grouped;

class Quote extends \Magento\Quote\Model\Quote
{
    /**
     * @var array
     */
    private $allVisibleItems;

    /**
     * Add product. Returns error message if product type instance can't prepare product.
     *
     * @param mixed $product
     * @param null|float|\Magento\Framework\DataObject $request
     * @param null|string $processMode
     * @return \Magento\Quote\Model\Quote\Item|string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function addProduct(
        \Magento\Catalog\Model\Product $product,
        $request = null,
        $processMode = \Magento\Catalog\Model\Product\Type\AbstractType::PROCESS_MODE_FULL
    ) {
        if ($request === null) {
            $request = 1;
        }
        if (is_numeric($request)) {
            $request = $this->objectFactory->create(['qty' => $request]);
        }
        if (!$request instanceof \Magento\Framework\DataObject) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('We found an invalid request for adding product to quote.')
            );
        }

        if (!$product->isSalable()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Product that you are trying to add is not available.')
            );
        }

        $cartCandidates = $product->getTypeInstance()->prepareForCartAdvanced($request, $product, $processMode);

        /**
         * Error message
         */
        if (is_string($cartCandidates) || $cartCandidates instanceof \Magento\Framework\Phrase) {
            return (string)$cartCandidates;
        }

        /**
         * If prepare process return one object
         */
        if (!is_array($cartCandidates)) {
            $cartCandidates = [$cartCandidates];
        }

        $parentItem = null;
        $errors = [];
        $item = null;
        $items = [];
        foreach ($cartCandidates as $candidate) {
            // Child items can be sticked together only within their parent
            $stickWithinParent = $candidate->getParentProductId() ? $parentItem : null;
            $candidate->setStickWithinParent($stickWithinParent);

            $item = $this->getItemByProduct($candidate);
            if (!$item) {
                $item = $this->itemProcessor->init($candidate, $request);
                $item->setQuote($this);
                $item->setOptions($candidate->getCustomOptions());
                $item->setProduct($candidate);
                // Add only item that is not in quote already
                $this->addItem($item);
            }
            $items[] = $item;

            /**
             * As parent item we should always use the item of first added product
             */
            if (!$parentItem) {
                $parentItem = $item;
            }
            /*
             * Group configurable product have multiple parent
             */
            if ($product->getTypeId()==Grouped::TYPE_CODE) {
                if ($item->getProduct()->getTypeId() == Configurable::TYPE_CODE) {
                    $parentItem = $item;
                }
            }
            if ($parentItem && $candidate->getParentProductId() && !$item->getParentItem()) {
                $item->setParentItem($parentItem);
            }

            $this->itemProcessor->prepare($item, $request, $candidate);

            // collect errors instead of throwing first one
            if ($item->getHasError()) {
                foreach ($item->getMessage(false) as $message) {
                    if (!in_array($message, $errors)) {
                        // filter duplicate messages
                        $errors[] = $message;
                    }
                }
            }
        }
        if (!empty($errors)) {
            throw new \Magento\Framework\Exception\LocalizedException(__(implode("\n", $errors)));
        }

        $this->_eventManager->dispatch('sales_quote_product_add_after', ['items' => $items]);
        return $parentItem;
    }

    /**
     * Check quote for virtual product only
     *
     * @return bool
     */
    public function isVirtual()
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/gggggg.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $isVirtual = true;
        $countItems = 0;
        foreach ($this->getItemsCollection() as $_item) {
            /* @var $_item \Magento\Quote\Model\Quote\Item */
            if ($_item->isDeleted() || $_item->getParentItemId() || $_item->getIsActive() == 0) {
                continue;
            }
            $countItems++;
            if (!$_item->getProduct()->getIsVirtual()) {
                $logger->info($_item->getId());
                $isVirtual = false;
                break;
            }
        }
        return $countItems == 0 ? false : $isVirtual;
    }

    /**
     * get items active & inactive
     * @return array
     */
    public function getItemsV2()
    {
        $items = [];
        foreach ($this->getItemsCollection() as $item) {
            if (!$item->isDeleted() && !$item->getParentItemId() && !$item->getParentItem() && !$item->getIsVirtual()) {
                $items[] = $item;
            }
        }
        return $items;
    }

    /**
     * Retrieve quote item by product id
     *
     * @param   \Magento\Catalog\Model\Product $product
     * @return  \Magento\Quote\Model\Quote\Item|bool
     */
    public function getItemByProduct($product)
    {
        foreach (parent::getAllItems() as $item) {
            if ($item->representProduct($product)) {
                return $item;
            }
        }
        return false;
    }

    /**
     * @override
     * @return \Magento\Quote\Model\Quote\Item[]
     */
    public function getAllItems()
    {
        $items = [];
        foreach ($this->getItemsCollection() as $item) {
            if (!$item->getId() || $item->getIsActive() === null) {
                $item->setIsActive(1);
            }

            if (!$item->isDeleted() && $item->getIsActive()) {
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * @override
     * @return \Magento\Quote\Model\Quote\Item[]
     */
    public function getAllVisibleItems()
    {
        if (empty($this->allVisibleItems)) {
            $items = [];
            foreach ($this->getItemsCollection() as $item) {
                if (!$item->getId() || $item->getIsActive() === null) {
                    $item->setIsActive(1);
                }

                if (!$item->isDeleted() && $item->getIsActive() && !$item->getParentItemId() && !$item->getParentItem()) {
                    $items[] = $item;
                }
            }

            $this->allVisibleItems = $items;
        }

        return $this->allVisibleItems;
    }

    /**
     * @param int $itemId
     * @param \Magento\Framework\DataObject $buyRequest
     * @param null $params
     * @return bool|\Magento\Quote\Model\Quote\Item|string|null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function updateItem($itemId, $buyRequest, $params = null)
    {
        $item = $this->getItemById($itemId);
        if (!$item) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('This is the wrong quote item id to update configuration.')
            );
        }
        $productId = $item->getProduct()->getId();

        //We need to create new clear product instance with same $productId
        //to set new option values from $buyRequest
        $product = clone $this->productRepository->getById($productId, false, $this->getStore()->getId());

        if (!$params) {
            $params = new \Magento\Framework\DataObject();
        } elseif (is_array($params)) {
            $params = new \Magento\Framework\DataObject($params);
        }
        $params->setCurrentConfig($item->getBuyRequest());
        $buyRequest = $this->_catalogProduct->addParamsToBuyRequest($buyRequest, $params);
        $buyRequest->setId($itemId);
        $buyRequest->setResetCount(true);
        $resultItem = $this->addProduct($product, $buyRequest);

        if (is_string($resultItem)) {
            throw new \Magento\Framework\Exception\LocalizedException(__($resultItem));
        }

        if ($resultItem->getParentItem()) {
            $resultItem = $resultItem->getParentItem();
        }

        if ($resultItem->getId() != $itemId) {
            /**
             * Product configuration didn't stick to original quote item
             * It either has same configuration as some other quote item's product or completely new configuration
             */
            $this->removeItem($itemId);
            $items = $this->getAllItems();
            foreach ($items as $item) {
                if ($item->getProductId() == $productId && $item->getId() != $resultItem->getId()) {
                    if ($resultItem->compare($item)) {
                        // Product configuration is same as in other quote item
                        $resultItem->setQty($resultItem->getQty() + $item->getQty());
                        $this->removeItem($item->getId());
                        break;
                    }
                }
            }
        } else {
            $resultItem->setQty($buyRequest->getQty());
        }

        return $resultItem;
    }

    /**
     * @override
     * @return \Magento\Quote\Model\Quote\Item[]
     */
    public function getAllVisibleItemsInCart()
    {
        $items = [];
        foreach ($this->getItemsCollection() as $item) {
            if (!$item->isDeleted() && !$item->getParentItemId() && !$item->getParentItem()) {
                $items[] = $item;
            }
        }
        return $items;
    }

    /**
     * Validate minimum amount.
     *
     * @param bool $multishipping
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function validateMinimumAmount($multishipping = false)
    {
        if (empty($this->getAllVisibleItems())) {
            return false;
        }

        return parent::validateMinimumAmount($multishipping);
    }
}
