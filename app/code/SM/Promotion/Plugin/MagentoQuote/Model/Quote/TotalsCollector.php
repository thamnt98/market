<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Promotion
 *
 * Date: September, 21 2020
 * Time: 5:58 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Promotion\Plugin\MagentoQuote\Model\Quote;

class TotalsCollector extends \Amasty\Promo\Plugin\Quote\Model\Quote\TotalsCollectorPlugin
{
    /**
     * @var \Amasty\Promo\Model\ItemRegistry\PromoItemRegistry
     */
    protected $promoItemRegistry;

    /**
     * @var \Amasty\Promo\Model\Storage
     */
    protected $storage;

    /**
     * @var \Amasty\Promo\Helper\Cart
     */
    protected $promoCartHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * TotalsCollector constructor.
     *
     * @param \Magento\Checkout\Model\Session                    $checkoutSesssion
     * @param \Amasty\Promo\Helper\Cart                          $promoCartHelper
     * @param \Amasty\Promo\Helper\Item                          $promoItemHelper
     * @param \Amasty\Promo\Model\Registry                       $promoRegistry
     * @param \Amasty\Promo\Model\Config                         $config
     * @param \Magento\Framework\Event\ManagerInterface          $eventManager
     * @param \Amasty\Promo\Model\ItemRegistry\PromoItemRegistry $promoItemRegistry
     * @param \Magento\Catalog\Model\ProductRepository           $productRepository
     * @param \Amasty\Promo\Model\Storage                        $storage
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSesssion,
        \Amasty\Promo\Helper\Cart $promoCartHelper,
        \Amasty\Promo\Helper\Item $promoItemHelper,
        \Amasty\Promo\Model\Registry $promoRegistry,
        \Amasty\Promo\Model\Config $config,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Amasty\Promo\Model\ItemRegistry\PromoItemRegistry $promoItemRegistry,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Amasty\Promo\Model\Storage $storage
    ) {
        $this->promoItemRegistry = $promoItemRegistry;
        $this->storage = $storage;
        $this->promoCartHelper = $promoCartHelper;

        parent::__construct(
            $promoCartHelper,
            $promoItemHelper,
            $promoRegistry,
            $config,
            $eventManager,
            $promoItemRegistry,
            $productRepository,
            $storage
        );
        $this->checkoutSession = $checkoutSesssion;
    }

    /**
     * @param \Magento\Quote\Model\Quote\TotalsCollector $subject
     * @param callable                                   $proceed
     * @param \Magento\Quote\Model\Quote                 $quote
     * @param \Magento\Quote\Model\Quote\Address         $address
     *
     * @return \Magento\Quote\Model\Quote\Address\Total
     */
    public function aroundCollectAddressTotals(
        \Magento\Quote\Model\Quote\TotalsCollector $subject,
        callable $proceed,
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Model\Quote\Address $address
    ) {
        $this->recollectTotals = false;

        if ($address->getAllItems() &&
            (
                !$quote->isMultipleShippingAddresses() ||
                ($quote->isMultipleShippingAddresses() && $this->checkoutSession->getMainOrder())
            )
        ) {
            $this->promoItemRegistry->resetQtyAllowed();
        }

        $totals = $proceed($quote, $address);

        if ($address->getAllItems()) {
            $this->updateQuoteItems($quote);
            if ($this->storage->isAutoAddAllowed()) {
                $this->addProductsAutomatically($quote);
            } elseif (!$this->recollectTotals && $this->promoItemRegistry->getItemsForAutoAdd()) {
                //save estimation address
                $this->storage->setIsQuoteSaveRequired(true);
            }

            if ($this->recollectTotals) {
                $this->promoCartHelper->updateTotalQty($quote);
                $address->unsetData('cached_items_all');
                $address->setCollectShippingRates(true);

                //execute closure one more time for recalculate totals
                $totals = $proceed($quote, $address);
                $this->storage->setIsQuoteSaveRequired(true);
            }
        }

        return $totals;
    }
}
