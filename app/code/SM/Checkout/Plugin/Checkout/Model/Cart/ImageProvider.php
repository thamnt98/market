<?php
/**
 * @category    SM
 * @package     SM_Customer
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Nam Nguyen <namnd2@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Checkout\Plugin\Checkout\Model\Cart;

use Magento\Checkout\CustomerData\DefaultItem;
use Magento\Framework\App\ObjectManager;

/**
 * Class ImageProvider
 * @package SM\Checkout\Plugin\Checkout\Model\Cart
 */
class ImageProvider
{
    /**
     * @var \Magento\Quote\Api\CartItemRepositoryInterface
     */
    protected $itemRepository;

    /**
     * @var \Magento\Checkout\CustomerData\ItemPoolInterface
     * @deprecated 100.2.7 No need for the pool as images are resolved in the default item implementation
     * @see \Magento\Checkout\CustomerData\DefaultItem::getProductForThumbnail
     */
    protected $itemPool;

    /**
     * @var \Magento\Checkout\CustomerData\DefaultItem
     * @since 100.2.7
     */
    protected $customerDataItem;

    /**
     * ImageProvider constructor.
     * @param \Magento\Quote\Api\CartItemRepositoryInterface $itemRepository
     * @param \Magento\Checkout\CustomerData\ItemPoolInterface $itemPool
     * @param DefaultItem|null $customerDataItem
     */
    public function __construct(
        \Magento\Quote\Api\CartItemRepositoryInterface $itemRepository,
        \Magento\Checkout\CustomerData\ItemPoolInterface $itemPool,
        \Magento\Checkout\CustomerData\DefaultItem $customerDataItem = null
    ) {
        $this->itemRepository = $itemRepository;
        $this->itemPool = $itemPool;
        $this->customerDataItem = $customerDataItem ?: ObjectManager::getInstance()->get(DefaultItem::class);
    }

    /**
     * @param \Magento\Checkout\Model\Cart\ImageProvider $subject
     * @param \Closure $proceed
     * @param int $cartId
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function aroundGetImages(
        \Magento\Checkout\Model\Cart\ImageProvider $subject,
        \Closure $proceed,
        $cartId
    ) {
        $proceed($cartId);
        $itemData = [];

        /** @see code/Magento/Catalog/Helper/Product.php */
        $items = $this->itemRepository->getList($cartId);
        /** @var \Magento\Quote\Model\Quote\Item $cartItem */
        foreach ($items as $cartItem) {
            $product = $cartItem->getProduct();
            $allData = $this->customerDataItem->getItemData($cartItem);
            $imageData = $allData['product_image'];
            $imageData['url'] = $product->getProductUrl();
            $weight = $cartItem->getQty() * $cartItem->getProduct()->getWeight();
            $imageData['weight'] = round($weight, 2);
            $imageData['is_spo'] = $product->getData('is_spo');
            $itemData[$cartItem->getItemId()] = $imageData;
        }
        return $itemData;
    }
}
