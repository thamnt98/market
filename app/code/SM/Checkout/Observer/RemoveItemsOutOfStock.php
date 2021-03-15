<?php
/**
 * Created by PhpStorm.
 * User: thelightsp
 * Date: 5/25/20
 * Time: 1:57 PM
 */

namespace SM\Checkout\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class RemoveItemsOutOfStock
 * @package SM\Checkout\Observer
 */
class RemoveItemsOutOfStock implements ObserverInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistryInterface;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory
     */
    private $quoteItemCollectionFactory;

    /**
     * @var \Magento\InventoryCatalog\Model\GetStockIdForCurrentWebsite
     */
    private $getStockIdForCurrentWebsite;

    /**
     * @var \Magento\InventorySalesApi\Api\GetProductSalableQtyInterface
     */
    private $getProductSalableQty;

    /**
     * RemoveItemsOutOfStock constructor.
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\InventorySalesApi\Api\GetProductSalableQtyInterface $getProductSalableQty
     * @param \Magento\InventoryCatalog\Model\GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\InventorySalesApi\Api\GetProductSalableQtyInterface $getProductSalableQty,
        \Magento\InventoryCatalog\Model\GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        $this->messageManager = $messageManager;
        $this->getProductSalableQty = $getProductSalableQty;
        $this->getStockIdForCurrentWebsite = $getStockIdForCurrentWebsite;
    }

    /**
     * @param EventObserver $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(EventObserver $observer)
    {
        $quote = $this->checkoutSession->getQuote();

        $quotePayment =  $quote->getPayment();
        if ($quotePayment->getMethod()) {
            $quotePayment->setMethod(null);
        }

        $quoteShippingAddress = $quote->getShippingAddress();
        if ($quoteShippingAddress->getShippingMethod()) {
            $quoteShippingAddress->setShippingMethod(null);
        }

        if ($quote->getIsMultiShipping()) {
            $quote->setIsMultiShipping(0);
            $extensionAttributes = $quote->getExtensionAttributes();
            if ($extensionAttributes && $extensionAttributes->getShippingAssignments()) {
                $extensionAttributes->setShippingAssignments([]);
            }
        }

        if ($quoteItems = $quote->getAllVisibleItems()) {
            $hasRemove = false;
            $hasUpdate = false;

            foreach ($quoteItems as $item) {
                $product = $item->getProduct();
                if (!$product->getIsSalable()) {
                    $quote->removeItem($item->getId());
                    $hasRemove = true;
                }

                $maxQty = $this->getSaleableQty($item->getProduct()->getSku());
                if ($item->getQty() >= $maxQty) {
                    $item->setQty((float)$maxQty);
                    $hasUpdate = true;
                }
            }

            if ($hasRemove || $hasUpdate) {
                if ($hasRemove) {
                    $this->messageManager->addSuccessMessage(
                        __("We found some out of stock items in your cart. We've removed the items for you.")
                    );
                }
            }

            $quote->setTotalsCollectedFlag(false);
            $this->quoteRepository->save($quote);
        }
    }

    /**
     * @param $productId
     * @return float
     */
    public function getItemStock($productId)
    {
        $stockItem = $this->stockRegistryInterface->getStockItem($productId);
        $stockQty = $this->stockRegistryInterface->getStockStatus($productId)->getQty();
        $minStockQty = $stockItem->getMinQty();

        return $stockQty - $minStockQty;
    }

    public function getBundleItemStock($quoteItem)
    {
        $arrQty = [];
        $quoteItemCollection = $this->quoteItemCollectionFactory->create();
        $quoteItemCollection->getSelect()
            ->where('parent_item_id=?', $quoteItem->getItemId());
        foreach ($quoteItemCollection as $item) {
            $minQty = $this->stockRegistryInterface->getStockItemBySku($item->getSku())->getMinQty();
            $stockQty = $this->stockRegistryInterface->getStockStatusBySku($item->getSku())->getQty();
            $arrQty[] = $stockQty - $minQty;
        }

        return min($arrQty);
    }

    /**
     * @param $item
     * @return float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getConfigItemStock($item)
    {
        $qty = 0;
        $quoteItemCollection = $this->quoteItemCollectionFactory->create();
        $quoteItemCollection->getSelect()
            ->where('parent_item_id=?', $item->getItemId());
        if (!empty($quoteItemCollection)) {
            foreach ($quoteItemCollection as $item) {
                $qty = $this->getConfigStock($item);
            }
        } else {
            $qty = $this->getConfigStock($item);
        }
        return $qty;
    }

    /**
     * @param $item
     * @return float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getConfigStock($item)
    {
        $minQty = $this->stockRegistryInterface->getStockItemBySku($item->getSku())->getMinQty();
        $stockQty = $this->stockRegistryInterface->getStockStatusBySku($item->getSku())->getQty();
        return $stockQty - $minQty;
    }

    /**
     * @param string $sku
     * @return float|int
     */
    protected function getSaleableQty(string $sku)
    {
        $stockId = $this->getStockIdForCurrentWebsite->execute();
        try {
            return $this->getProductSalableQty->execute($sku, $stockId);
        } catch (InputException $e) {
            return 0;
        } catch (LocalizedException $e) {
            return 0;
        }
    }
}
