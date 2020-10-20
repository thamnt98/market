<?php
/**
 * Created by PhpStorm.
 * User: thelightsp
 * Date: 5/7/20
 * Time: 6:21 PM
 */

namespace SM\Checkout\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class BeforeCreateMainOrder
 * @package SM\Checkout\Observer
 */
class ActiveItems implements ObserverInterface
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
     * ActiveItems constructor.
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $request = $observer->getEvent()->getRequest();
        $quote = $this->checkoutSession->getQuote();
        if ($request->getFullActionName() == 'checkout_cart_index' && $quote->getIsVirtual()) {
            foreach ($quote->getItemsCollection() as $item) {
                if (!$item->getIsVirtual() && $item->getIsActive() == 0) {
                    $item->setIsActive(1);
                } elseif ($item->getIsVirtual() && $item->getIsActive() == 1) {
                    $item->setIsActive(0);
                }
            }
            $this->quoteRepository->save($quote->collectTotals());
        }
    }
}
