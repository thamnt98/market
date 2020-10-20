<?php
/**
 * Created by PhpStorm.
 * User: thelightsp
 * Date: 5/14/20
 * Time: 5:07 PM
 */

namespace SM\Checkout\Observer;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\SalesRule\Api\Exception\CodeRequestLimitException;
use Magento\SalesRule\Model\Spi\CodeLimitManagerInterface;

/**
 * Validate newly provided coupon code before using it while calculating totals.
 */
class CouponCodeValidation implements ObserverInterface
{
    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $criteriaBuilder;

    /**
     * @var CodeLimitManagerInterface
     */
    private $codeLimitManager;

    /**
     * @param CodeLimitManagerInterface $codeLimitManager
     * @param CartRepositoryInterface $cartRepository
     * @param SearchCriteriaBuilder $criteriaBuilder
     */
    public function __construct(
        CodeLimitManagerInterface $codeLimitManager,
        CartRepositoryInterface $cartRepository,
        SearchCriteriaBuilder $criteriaBuilder
    ) {
        $this->codeLimitManager = $codeLimitManager;
        $this->cartRepository = $cartRepository;
        $this->criteriaBuilder = $criteriaBuilder;
    }

    /**
     * override from \Magento\SalesRule\Observer\CouponCodeValidation
     * @param EventObserver $observer
     * @throws CodeRequestLimitException
     */
    public function execute(EventObserver $observer)
    {
        /** @var Quote $quote */
        $quote = $observer->getData('quote');
        $codes = explode(',', $quote->getCouponCode());
        if (count($codes) > 0) {
            /** @var Quote[] $found */
            $found = $this->cartRepository->getList(
                $this->criteriaBuilder->addFilter('main_table.' . CartInterface::KEY_ENTITY_ID, $quote->getId())
                    ->create()
            )->getItems();

            $multipleCoupon = [];
            if (($found[0]->getCouponCode()) != null) {
                $multipleCoupon = explode(',', $found[0]->getCouponCode());
            }

            foreach ($codes as $code) {
                if (!$found || !in_array($code, $multipleCoupon)) {
                    try {
                        $this->codeLimitManager->checkRequest($code);
                    } catch (CodeRequestLimitException $exception) {
                        unset($codes[$code]);
                        $newCodes = implode(',', $codes);
                        $quote->setCouponCode($newCodes);
                        throw $exception;
                    }
                }
            }
        }
    }
}
