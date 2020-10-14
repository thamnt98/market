<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Sprint\Model;

use Magento\Framework\Exception\LocalizedException;
use Trans\Sprint\Api\ServiceFeeInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Serivce Fee.
 */
class ServiceFee implements ServiceFeeInterface
{
    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * Constructs a coupon read service object.
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository Quote repository.
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @inheritDoc
     */
    public function get($cartId)
    {
        /** @var  \Magento\Quote\Model\Quote $quote */
        try {
            $quote = $this->quoteRepository->getActive($cartId);
            return $quote->getData('service_fee');
        } catch (NoSuchEntityException $e) {
            throw new NoSuchEntityException(__('Data doesn\'t exists.'));
        }
    }

    /**
     * @inheritDoc
     */
    public function setServiceFee($cartId, $serviceFeeValue = 0)
    {
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('The "%1" Cart doesn\'t contain products.', $cartId));
        }
        if (!$quote->getStoreId()) {
            throw new NoSuchEntityException(__('Cart isn\'t assigned to correct store'));
        }
        $quote->getShippingAddress()->setCollectShippingRates(true);
        $serviceFee = ((int)$quote->getGrandTotal() * $serviceFeeValue)/100;

        try {
            $quote->setData('service_fee', $serviceFee);
            // $this->quoteRepository->save($quote->collectTotals());
            $this->quoteRepository->save($quote);
        } catch (LocalizedException $e) {
            throw new CouldNotSaveException(__('The service fee couldn\'t be applied: ' .$e->getMessage()), $e);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __("The service fee couldn't be applied."),
                $e
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function remove($cartId)
    {
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('The "%1" Cart doesn\'t contain products.', $cartId));
        }
        $quote->getShippingAddress()->setCollectShippingRates(true);
        try {
            $quote->setCouponCode('');
            $this->quoteRepository->save($quote->collectTotals());
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(
                __("The coupon code couldn't be deleted. Verify the coupon code and try again.")
            );
        }
        if ($quote->getCouponCode() != '') {
            throw new CouldNotDeleteException(
                __("The coupon code couldn't be deleted. Verify the coupon code and try again.")
            );
        }
        return true;
    }
}
