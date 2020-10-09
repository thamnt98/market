<?php
/**
 * Class ReOrderRepository
 * @package SM\DigitalProduct\Model\Cart
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\DigitalProduct\Model\Cart;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\ProductOptionExtensionInterfaceFactory;
use Magento\Quote\Api\Data\ProductOptionInterfaceFactory;
use Magento\Quote\Model\ResourceModel\Quote as QuoteResource;
use Magento\Setup\Exception;
use SM\DigitalProduct\Api\Data\DigitalInterfaceFactory;
use SM\DigitalProduct\Api\Inquire\Data\ElectricityBillInterface;
use SM\DigitalProduct\Api\ReorderRepositoryInterface;

class ReOrderRepository extends CartRepository implements ReorderRepositoryInterface
{
    /**
     * @var \SM\DigitalProduct\Api\DigitalAPIProcessorInterface[]
     */
    private $processorPool;

    /**
     * ReOrderRepository constructor.
     * @param CartRepositoryInterface $quoteRepository
     * @param QuoteResource $quoteResource
     * @param ProductOptionInterfaceFactory $productOptionFactory
     * @param ProductOptionExtensionInterfaceFactory $productOptionExtensionFactory
     * @param DigitalInterfaceFactory $digitalDataFactory
     * @param ProductRepositoryInterface $product
     * @param array $processorPool
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        QuoteResource $quoteResource,
        ProductOptionInterfaceFactory $productOptionFactory,
        ProductOptionExtensionInterfaceFactory $productOptionExtensionFactory,
        DigitalInterfaceFactory $digitalDataFactory,
        ProductRepositoryInterface $product,
        array $processorPool = []
    ) {
        $this->processorPool = $processorPool;
        parent::__construct(
            $quoteRepository,
            $quoteResource,
            $productOptionFactory,
            $productOptionExtensionFactory,
            $digitalDataFactory,
            $product
        );
    }

    /**
     * @inheritDoc
     */
    public function reOrder($customerId, $cartId, \Magento\Quote\Api\Data\CartItemInterface $cartItem, $quote = null)
    {
        try {
            $inquire = $this->inquire($customerId, $cartItem);
            if ($inquire !== false) {
                return parent::addToCart($cartId, $cartItem, $quote);
            }
        } catch (\Exception $e) {
            throw new \Magento\Framework\Webapi\Exception(
                __($e->getMessage()),
                0,
                \Magento\Framework\Webapi\Exception::HTTP_BAD_REQUEST
            );
        }

        throw new \Magento\Framework\Webapi\Exception(
            __("Something went wrong. Please refresh page and try again."),
            0,
            \Magento\Framework\Webapi\Exception::HTTP_BAD_REQUEST
        );
    }

    /**
     * @param $customerId
     * @param \Magento\Quote\Api\Data\CartItemInterface $cartItem
     * @return bool
     * @throws \Exception
     */
    private function inquire($customerId, \Magento\Quote\Api\Data\CartItemInterface $cartItem)
    {
        if ($cartItem->getProductOption()
            && $cartItem->getProductOption()->getExtensionAttributes()) {
            $serviceType = $cartItem->getProductOption()->getExtensionAttributes()->getServiceType();
            if (array_key_exists($serviceType, $this->processorPool)) {
                $inquire = $this->processorPool[$serviceType]->inquire($customerId, $cartItem);
                if (is_object($inquire)) {
                    if ($inquire->getResponseCode() == self::SUCCESS) {
                        if ($serviceType == \SM\DigitalProduct\Helper\Category\Data::ELECTRICITY_BILL_VALUE) {
                            $this->prepareDataElectricityBill($cartItem, $inquire);
                        }
                        return true;
                    } else {
                        throw new \Exception($this->getErrorMessage($inquire->getResponseCode()));
                    }
                }
                throw new \Exception($this->getErrorMessage());
            } else {
                return true;
            }
        }
        throw new \Exception($this->getErrorMessage());
    }

    /**
     * @param $responseCode
     * @return \Magento\Framework\Phrase
     */
    private function getErrorMessage($responseCode = null)
    {
        $responses = [
            self::TIMEOUT_RESPONSE_CODE => __("Please check the internet connection and try again"),
            self::PROVIDER_CUT_OFF => __("This service is not available during cut off/maintenance time"),
            self::ALREADY_PAID => __("Your bill has already been paid"),
            self::ERROR => __("Make sure you enter the correct number")
        ];

        $defaultMessage = __('Something went wrong. Please refresh page and try again.');

        return array_key_exists($responseCode, $responses) ? $responses[$responseCode] : $defaultMessage;
    }

    /**
     * @param \Magento\Quote\Api\Data\CartItemInterface $cartItem
     * @param $inquire
     */
    private function prepareDataElectricityBill(\Magento\Quote\Api\Data\CartItemInterface $cartItem, $inquire)
    {
        $digitalData = $cartItem->getProductOption()->getExtensionAttributes()->getDigital();
        $keysNoNeedToReplace = [ElectricityBillInterface::CUSTOMER_ID];

        foreach ($inquire->getData() as $key => $value) {
            if (!in_array($key, $keysNoNeedToReplace)) {
                $digitalData->setData($key, $value);
            }
        }
    }
}
