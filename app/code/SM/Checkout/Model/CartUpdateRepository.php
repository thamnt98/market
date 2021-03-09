<?php

namespace SM\Checkout\Model;

use Magento\Quote\Api\CartItemRepositoryInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use mysql_xdevapi\Exception;
use SM\Checkout\Api\CartUpdateRepositoryInterface;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Quote\Model\Cart\Totals\ItemConverter;
use Magento\Quote\Model\Cart\TotalsConverter;
use Magento\Quote\Api\CouponManagementInterface;

class CartUpdateRepository implements CartUpdateRepositoryInterface
{

    const ITEM_IS_ACTIVE = 1;
    const ITEM_IS_INACTIVE = 0;

    /**
     * @var Quote
     */
    protected $quote;

    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var CartItemRepositoryInterface
     */
    private $cartItemRepository;

    /**
     * @var \Magento\Quote\Api\Data\TotalsInterfaceFactory
     */
    private $totalsFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var TotalsConverter
     */
    private $totalsConverter;

    /**
     * @var ItemConverter
     */
    private $itemConverter;

    /**
     * @var CouponManagementInterface
     */
    private $couponService;

    /**
     * @var
     */
    protected $registry;

    /**
     * Update constructor.
     * @param CartRepositoryInterface $quoteRepository
     * @param CartItemRepositoryInterface $cartItemRepository
     */
    public function __construct(
        \Magento\Quote\Api\Data\TotalsInterfaceFactory $totalsFactory,
        DataObjectHelper $dataObjectHelper,
        CartRepositoryInterface $quoteRepository,
        CartItemRepositoryInterface $cartItemRepository,
        TotalsConverter $totalsConverter,
        ItemConverter $converter,
        CouponManagementInterface $couponService,
        \Magento\Framework\Registry $registry
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->cartItemRepository = $cartItemRepository;
        $this->totalsFactory = $totalsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->totalsConverter = $totalsConverter;
        $this->itemConverter = $converter;
        $this->couponService = $couponService;
        $this->registry = $registry;
    }

    /**
     * @param $cartId
     * @param $check
     * @return bool
     * @throws \Exception
     */
    public function selectAll($cartId, $check)
    {
        try {
            if (isset($check)) {
                $checked = self::ITEM_IS_INACTIVE;
                if ($check == self::ITEM_IS_ACTIVE) {
                    $checked = self::ITEM_IS_ACTIVE;
                }
                $this->updateItemActive($cartId, $checked);
                return true;
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param $cartId
     * @param $itemIds
     * @return bool
     * @throws \Exception
     */
    public function removeIds($cartId, $itemIds)
    {
        try {
            if (!$this->registry->registry("remove_cart_item")) {
                $this->registry->register("remove_cart_item", true);
            }
            $removeIds = isset($itemIds) ? explode(',', $itemIds) : [];
            if (!empty($removeIds)) {
                foreach ($removeIds as $id) {
                    $this->getQuote($cartId)->removeItem($id);
                }
                $this->getQuote($cartId)->setTotalsCollectedFlag(true)->collectTotals();
                $this->quoteRepository->save($this->getQuote($cartId));
                $this->registry->unregister("remove_cart_item");
                return true;
            }
        } catch (\Exception $e) {
            $this->registry->unregister("remove_cart_item");
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param $cartId
     * @param $itemId
     * @param $check
     * @return bool
     * @throws \Exception
     */
    public function selectItem($cartId, $itemId, $check)
    {
        try {
            if (isset($itemId) && isset($qty)) {
                $this->updateItemActiveById($cartId, $check, $itemId);
                return true;
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @inheritdoc
     */
    public function updateSelectedItem($cartId, $items)
    {
        $quote = $this->getQuote($cartId);
        $saveItems = [];
        $itemsQty = 0;
        try {
            foreach ($quote->getItemsCollection() as $quoteItem) {
                if (!$quoteItem->isDeleted() && !$quoteItem->getParentItemId() && !$quoteItem->getParentItem()) {
                    foreach ($items as $item) {
                        if ($item->getItemId() == $quoteItem->getItemId()) {
                            $quoteItem->setIsActive($item->getIsChecked());
                            $itemsQty += $item->getIsChecked() ? $quoteItem->getQty() : 0;
                        }
                    }
                }
            }

            if (!empty($items)) {
                $this->quoteRepository->save($quote);
            }

            if ($quote->isVirtual()) {
                $addressTotalsData = $quote->getBillingAddress()->getData();
                $addressTotals = $quote->getBillingAddress()->getTotals();
            } else {
                $addressTotalsData = $quote->getShippingAddress()->getData();
                $addressTotals = $quote->getShippingAddress()->getTotals();
            }
            unset($addressTotalsData[ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY]);

            /** @var \Magento\Quote\Api\Data\TotalsInterface $quoteTotals */
            $quoteTotals = $this->totalsFactory->create();

            $this->dataObjectHelper->populateWithArray(
                $quoteTotals,
                $addressTotalsData,
                \Magento\Quote\Api\Data\TotalsInterface::class
            );
            $items = [];
            foreach ($quote->getItems() as $index => $item) {
                $items[$index] = $this->itemConverter->modelToDataObject($item);
            }
            $calculatedTotals = $this->totalsConverter->process($addressTotals);
            $quoteTotals->setTotalSegments($calculatedTotals);

            $amount = $quoteTotals->getGrandTotal() - $quoteTotals->getTaxAmount();
            $amount = $amount > 0 ? $amount : 0;
            $quoteTotals->setCouponCode($this->couponService->get($cartId));
            $quoteTotals->setGrandTotal($amount);
            $quoteTotals->setItems($items);
            $quoteTotals->setItemsQty($itemsQty);
            $quoteTotals->setBaseCurrencyCode($quote->getBaseCurrencyCode());
            $quoteTotals->setQuoteCurrencyCode($quote->getQuoteCurrencyCode());
            return $quoteTotals;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param $quoteId
     * @param $checked
     * @param $id
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function updateItemActiveById($quoteId, $checked, $id)
    {
        $quoteItem = $this->getQuote($quoteId)->getItemById($id);
        if ($quoteItem) {
            $quoteItem->setIsActive($checked);
            $this->cartItemRepository->save($quoteItem);
        }
    }

    /**
     * @param $quoteId
     * @param $checked
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function updateItemActive($quoteId, $checked)
    {
        foreach ($this->getQuote($quoteId)->getItemsCollection() as $item) {
            if ($item->getIsActive() != $checked) {
                $item->setIsActive($checked);
                $item->save();
            }
        }
    }

    /**
     * @param $quoteId
     * @return \Magento\Quote\Api\Data\CartInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getQuote($quoteId)
    {
        return $this->quoteRepository->get($quoteId);
    }
}
