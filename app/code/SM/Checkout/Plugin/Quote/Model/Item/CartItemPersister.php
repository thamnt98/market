<?php
/**
 * Class Search
 * @package SM\Checkout\Plugin\Quote\Item
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\Checkout\Plugin\Quote\Model\Item;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\Exception;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Model\Quote\Item\CartItemOptionsProcessor;

class CartItemPersister
{
    const MAXIMUM_QTY_WHOLE_CART = 99;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var CartItemOptionsProcessor
     */
    private $cartItemOptionProcessor;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param CartItemOptionsProcessor $cartItemOptionProcessor
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        CartItemOptionsProcessor $cartItemOptionProcessor,
        \Magento\Framework\Registry $registry
    ) {
        $this->productRepository = $productRepository;
        $this->cartItemOptionProcessor = $cartItemOptionProcessor;
        $this->registry = $registry;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item\CartItemPersister $subject
     * @param callable $proceed
     * @param CartInterface $quote
     * @param CartItemInterface $item
     * @return CartItemInterface|boolean
     * @throws CouldNotSaveException
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function aroundSave(
        \Magento\Quote\Model\Quote\Item\CartItemPersister $subject,
        callable $proceed,
        CartInterface $quote,
        CartItemInterface $item
    ) {

        /** @var \Magento\Quote\Model\Quote $quote */
        $qty = $item->getQty();
        if (!is_numeric($qty) || $qty <= 0) {
            throw InputException::invalidFieldValue('qty', $qty);
        }
        $cartId = $item->getQuoteId();
        $itemId = $item->getItemId();
        try {
            /** Update existing item */
            if (isset($itemId)) {
                $currentItem = $quote->getItemById($itemId);
                if (!$currentItem) {
                    throw new NoSuchEntityException(
                        __('The %1 Cart doesn\'t contain the %2 item.', $cartId, $itemId)
                    );
                }
                $productType = $currentItem->getProduct()->getTypeId();
                $buyRequestData = $this->cartItemOptionProcessor->getBuyRequest($productType, $item);
                if (is_object($buyRequestData)) {
                    /** Update item product options */
                    $item = $quote->updateItem($itemId, $buyRequestData);
                } else {
                    if ($item->getQty() !== $currentItem->getQty()) {
                        $currentItem->setQty($qty);
                        /**
                         * Qty validation errors are stored as items message
                         * @see \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator::validate
                         */
                        if (!empty($currentItem->getMessage())) {
                            throw new LocalizedException(__($currentItem->getMessage()));
                        }
                    }
                }
            } else {
                if (!$this->registry->registry("remove_cart_item")) {
                    /** add new item to shopping cart */
                    $product = $this->productRepository->get($item->getSku());
                    $productType = $product->getTypeId();
                    $itemQtyCart = (int)$quote->getItemsQty();
                    $price = $item->getPrice();

                    if ($itemQtyCart + (int)$item->getQty() > self::MAXIMUM_QTY_WHOLE_CART) {
                        throw new Exception(
                            __(sprintf('The maximum number of the whole cart does not exceed %s', self::MAXIMUM_QTY_WHOLE_CART)),
                            Exception::HTTP_BAD_REQUEST,
                            Exception::HTTP_BAD_REQUEST
                        );
                    }

                    $item = $quote->addProduct(
                        $product,
                        $this->cartItemOptionProcessor->getBuyRequest($productType, $item)
                    );

                    if ($productType == \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL && $price) {
                        $item->setCustomPrice($price);
                        $item->setOriginalCustomPrice($price);
                    }

                    if (is_string($item)) {
                        throw new LocalizedException(__($item));
                    }
                }
            }
        } catch (NoSuchEntityException $e) {
            throw $e;
        } catch (LocalizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__("The quote couldn't be saved."));
        }
        $itemId = $item->getId();
        foreach ($quote->getAllItems() as $quoteItem) {
            /** @var \Magento\Quote\Model\Quote\Item $quoteItem */
            if ($itemId == $quoteItem->getId()) {
                $item = $this->cartItemOptionProcessor->addProductOptions($productType, $quoteItem);
                return $this->cartItemOptionProcessor->applyCustomOptions($item);
            }
        }

        return false;
    }
}
