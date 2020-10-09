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
     * RemoveItemsOutOfStock constructor.
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistryInterface
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory $quoteItemCollectionFactory
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistryInterface,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory $quoteItemCollectionFactory
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->stockRegistryInterface = $stockRegistryInterface;
        $this->quoteRepository = $quoteRepository;
        $this->messageManager = $messageManager;
        $this->quoteItemCollectionFactory = $quoteItemCollectionFactory;
    }

    /**
     * @param EventObserver $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(EventObserver $observer)
    {
        $quote = $this->checkoutSession->getQuote();

        $quote->getPayment()->setMethod(null);
        $quote->getShippingAddress()->setShippingMethod(null);

        if ($quote->getIsMultiShipping()) {
            $quote->setIsMultiShipping(0);
            $extensionAttributes = $quote->getExtensionAttributes();
            if ($extensionAttributes && $extensionAttributes->getShippingAssignments()) {
                $extensionAttributes->setShippingAssignments([]);
            }
        }

        if (($quote->getAllVisibleItems()) > 0) {
            $hasRemove = false;
            $hasUpdate = false;

            foreach ($quote->getAllVisibleItems() as $item) {
                $isOutOfStock = $this->stockRegistryInterface->getProductStockStatus($item->getProduct()->getId());

                if (!$isOutOfStock) {
                    $quote->removeItem($item->getId());
                    $hasRemove = true;
                }


                switch ($item->getProduct()->getTypeId()) {
                    case \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE:
                        $maxqty = $this->getConfigItemStock($item);
                        break;
                    case \Magento\Bundle\Model\Product\Type::TYPE_CODE:
                        $maxqty = $this->getBundleItemStock($item);
                        break;
                    default:
                        $maxqty = $this->getItemStock($item->getProduct()->getId());
                }

                if ($item->getQty() >= $maxqty) {
                    $item->setQty($maxqty);
                    $hasUpdate = true;
                }
            }

            if ($hasRemove || $hasUpdate) {
                $quote->setTotalsCollectedFlag(false)->collectTotals();

                if ($hasRemove) {
                    $this->messageManager->addSuccessMessage(
                        __("We found some out of stock items in your cart. We've removed the items for you.")
                    );
                }
            }
        }

        $this->quoteRepository->save($quote);

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
}
