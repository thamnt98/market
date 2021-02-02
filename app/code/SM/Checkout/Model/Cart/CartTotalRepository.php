<?php
/**
 * Class CartTotalRepository
 * @package SM\Checkout\Model\Cart
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */


namespace SM\Checkout\Model\Cart;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\CouponManagementInterface;
use Magento\Quote\Model\Cart\Totals\ItemConverter;
use Magento\Quote\Model\Cart\TotalsConverter;
use SM\Checkout\Api\CartTotalRepositoryInterface;

class CartTotalRepository extends \Magento\Quote\Model\Cart\CartTotalRepository implements CartTotalRepositoryInterface
{
    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Quote\Api\Data\TotalsInterfaceFactory
     */
    protected $totalsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * CartTotalRepository constructor.
     * @param \Magento\Quote\Api\Data\TotalsInterfaceFactory $totalsFactory
     * @param CartRepositoryInterface $quoteRepository
     * @param DataObjectHelper $dataObjectHelper
     * @param CouponManagementInterface $couponService
     * @param TotalsConverter $totalsConverter
     * @param ItemConverter $converter
     */
    public function __construct(
        \Magento\Quote\Api\Data\TotalsInterfaceFactory $totalsFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        DataObjectHelper $dataObjectHelper,
        CouponManagementInterface $couponService,
        TotalsConverter $totalsConverter,
        ItemConverter $converter
    ) {
        parent::__construct(
            $totalsFactory,
            $quoteRepository,
            $dataObjectHelper,
            $couponService,
            $totalsConverter,
            $converter
        );
        $this->quoteRepository = $quoteRepository;
        $this->totalsFactory = $totalsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * @inheritdoc
     */
    public function get($cartId)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
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

        $calculatedTotals = $this->totalsConverter->process($addressTotals);
        $quoteTotals->setTotalSegments($calculatedTotals);

        $amount = $quoteTotals->getGrandTotal() - $quoteTotals->getTaxAmount();
        $amount = $amount > 0 ? $amount : 0;
        $quoteTotals->setCouponCode($this->couponService->get($cartId));
        $quoteTotals->setGrandTotal($amount);
        $quoteTotals->setItemsQty($quote->getItemsQty());
        $quoteTotals->setBaseCurrencyCode($quote->getBaseCurrencyCode());
        $quoteTotals->setQuoteCurrencyCode($quote->getQuoteCurrencyCode());

        return $quoteTotals;
    }
}
