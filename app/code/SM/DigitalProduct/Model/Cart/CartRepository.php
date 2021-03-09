<?php
/**
 * Class CartRepository
 * @package SM\DigitalProduct\Model\Cart
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\DigitalProduct\Model\Cart;

use Magento\Framework\Exception\InputException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\ProductOptionExtensionInterfaceFactory;
use Magento\Quote\Api\Data\ProductOptionInterfaceFactory;
use Magento\Quote\Model\ResourceModel\Quote as QuoteResource;
use Magento\Catalog\Api\ProductRepositoryInterface;
use SM\DigitalProduct\Api\Data\DigitalInterfaceFactory;
use SM\DigitalProduct\Api\Data\DigitalInterface;

class CartRepository implements \SM\DigitalProduct\Api\CartRepositoryInterface
{
    const ORDER_DETAIL_FUNCTION_PREFIX = "handleOrderDetail";

    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var QuoteResource
     */
    protected $quoteResource;

    /**
     * @var ProductOptionInterfaceFactory
     */
    protected $productOptionFactory;

    /**
     * @var ProductOptionExtensionInterfaceFactory
     */
    protected $productOptionExtensionFactory;

    /**
     * @var DigitalInterfaceFactory
     */
    protected $digitalDataFactory;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Magento\Catalog\Api\Data\ProductInterface
     */
    private $product;

    /**
     * @var \Magento\Quote\Api\Data\CartItemInterface
     */
    private $cartItem;

    /**
     * CartRepository constructor.
     * @param CartRepositoryInterface $quoteRepository
     * @param QuoteResource $quoteResource
     * @param ProductOptionInterfaceFactory $productOptionFactory
     * @param ProductOptionExtensionInterfaceFactory $productOptionExtensionFactory
     * @param DigitalInterfaceFactory $digitalDataFactory
     * @param ProductRepositoryInterface $product
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        QuoteResource $quoteResource,
        ProductOptionInterfaceFactory $productOptionFactory,
        ProductOptionExtensionInterfaceFactory $productOptionExtensionFactory,
        DigitalInterfaceFactory $digitalDataFactory,
        ProductRepositoryInterface $product
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->quoteResource = $quoteResource;
        $this->productOptionFactory = $productOptionFactory;
        $this->productOptionExtensionFactory = $productOptionExtensionFactory;
        $this->digitalDataFactory = $digitalDataFactory;
        $this->productRepository = $product;
    }

    /**
     * @inheritDoc
     */
    public function addToCart($cartId, \Magento\Quote\Api\Data\CartItemInterface $cartItem, $quote = null)
    {
        try {
            /** @var \Magento\Quote\Model\Quote $quote */
            if (!$cartId && !$quote) {
                throw new InputException(
                    __('"%fieldName" is required. Enter and try again.', ['fieldName' => 'cartId'])
                );
            }

            if ($quote === null || !$quote instanceof \Magento\Quote\Model\Quote) {
                $quote = $this->quoteRepository->getActive($cartId);
            }

            foreach ($quote->getItemsCollection() as $quoteItem) {
                if (!$quoteItem->getIsVirtual()) {
                    $quoteItem->setIsActive(0);
                    $quoteItems[] = $quoteItem;
                } else {
                    $quote->removeItem($quoteItem->getId());
                }
            }
            $quote->setIsVirtual(1);
            $this->updateQuote($quote, $cartItem);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Webapi\Exception(
                __($e->getMessage()),
                0,
                \Magento\Framework\Webapi\Exception::HTTP_BAD_REQUEST
            );
        }

        return true;
    }

    /**
     * @param $quote
     * @param $cartItem
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    protected function updateQuote($quote, $cartItem)
    {
        if ($quote->hasDataChanges()) {
            // For update item
            $this->quoteResource->save($quote->setTotalsCollectedFlag(true)->collectTotals());
        }

        $cartItem->setQty(1);
        $this->handleOrderDetails($cartItem);

        $quoteItems[] = $cartItem;
        $quote->setItems($quoteItems);
        // For new item
        $this->quoteRepository->save($quote);
    }

    /**
     * @param $cartItem
     */
    private function handleOrderDetails($cartItem)
    {
        $digitalData = $cartItem->getProductOption()->getExtensionAttributes()->getDigital();
        $serviceType = $cartItem->getProductOption()->getExtensionAttributes()->getServiceType();

        $serviceTypeFunction = self::ORDER_DETAIL_FUNCTION_PREFIX . $this->underscoreToCamelCase($serviceType);
        if (method_exists($this, $serviceTypeFunction)) {
            $this->cartItem = $cartItem;
            $cartItem->getProductOption()
                ->getExtensionAttributes()
                ->setDigital($this->$serviceTypeFunction($digitalData));
        }
    }

    /**
     * @param $digitalData
     * @return mixed
     */
    private function handleOrderDetailElectricityToken($digitalData)
    {
        $digitalDataCorrect = $this->cloneDigitalData($digitalData);
        $product = $this->productRepository->get($this->cartItem->getSku());
        if (!$digitalData->getPrice()) {
            $digitalData->setPrice($this->cartItem->getPrice());
        }

        if ($product->getSpecialPrice()) {
            $price = $product->getPriceInfo()->getPrice('final_price')->getValue();
        } else {
            $price = $product->getPrice();
        }

        $data = [
            DigitalInterface::SERVICE_TYPE => $digitalData->getData(DigitalInterface::SERVICE_TYPE),
            DigitalInterface::CUSTOMER_ID => $digitalData->getData(DigitalInterface::CUSTOMER_ID),
            DigitalInterface::MATERIAL_NUMBER => $digitalData->getData(DigitalInterface::MATERIAL_NUMBER),
            DigitalInterface::CUSTOMER_NAME => $digitalData->getData(DigitalInterface::CUSTOMER_NAME),
            DigitalInterface::POWER => $digitalData->getData(DigitalInterface::POWER),
            DigitalInterface::PRODUCT_NAME => $product->getName(),
            DigitalInterface::INFORMATION => $digitalData->getData(DigitalInterface::INFORMATION),
            DigitalInterface::PRICE => $price,
        ];

        return $digitalDataCorrect->setData($this->filterDigitalData($data));
    }

    /**
     * @param $digitalData
     * @return mixed
     */
    private function handleOrderDetailElectricityBill($digitalData)
    {
        $digitalDataCorrect = $this->cloneDigitalData($digitalData);

        $data = [
            DigitalInterface::SERVICE_TYPE => $digitalData->getData(DigitalInterface::SERVICE_TYPE),
            DigitalInterface::CUSTOMER_ID => $digitalData->getData(DigitalInterface::CUSTOMER_ID),
            DigitalInterface::CUSTOMER_NAME => $digitalData->getData(DigitalInterface::CUSTOMER_NAME),
            DigitalInterface::POWER => $digitalData->getData(DigitalInterface::POWER),
            DigitalInterface::PERIOD => $digitalData->getData(DigitalInterface::PERIOD),
            DigitalInterface::INFORMATION => $digitalData->getData(DigitalInterface::INFORMATION),
            DigitalInterface::BILL => $digitalData->getData(DigitalInterface::BILL),
            DigitalInterface::PENALTY => (int) $digitalData->getData(DigitalInterface::PENALTY),
            DigitalInterface::INCENTIVE_AND_TAX_FEE => (int) $digitalData->getData(DigitalInterface::INCENTIVE_AND_TAX_FEE),
            DigitalInterface::ADMIN_FEE => $digitalData->getData(DigitalInterface::ADMIN_FEE),
            DigitalInterface::SUBTOTAL => $this->cartItem->getPrice()
        ];

        return $digitalDataCorrect->setData($this->filterDigitalData($data));
    }

    /**
     * @param $digitalData
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function handleOrderDetailTopup($digitalData)
    {
        $product = $this->productRepository->get($this->cartItem->getSku());
        $digitalDataCorrect = $this->cloneDigitalData($digitalData);

        if (!$digitalData->getPrice()) {
            $digitalData->setPrice($this->cartItem->getPrice());
        }

        $data = [
            DigitalInterface::SERVICE_TYPE => $digitalData->getData(DigitalInterface::SERVICE_TYPE),
            DigitalInterface::MOBILE_NUMBER => $digitalData->getData(DigitalInterface::MOBILE_NUMBER),
            DigitalInterface::OPERATOR => $digitalData->getData(DigitalInterface::OPERATOR),
            DigitalInterface::PRODUCT_NAME => $product->getName(),
            DigitalInterface::PRICE => $digitalData->getData(DigitalInterface::PRICE),
        ];
        return $digitalDataCorrect->setData($this->filterDigitalData($data));
    }

    /**
     * @param $digitalData
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function handleOrderDetailMobilepackageInternet($digitalData)
    {
        return $this->handleOrderDetailTopup($digitalData);
    }

    /**
     * @param $digitalData
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function handleOrderDetailMobilepackageRoaming($digitalData)
    {
        return $this->handleOrderDetailTopup($digitalData);
    }

    /**
     * @param $digitalData
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function handleOrderDetailMobilepackage($digitalData)
    {
        return $this->handleOrderDetailTopup($digitalData);
    }

    /**
     * @param $input
     * @param string $separator
     * @return string|string[]
     */
    public function underscoreToCamelCase($input, $separator = '_')
    {
        return str_replace($separator, '', ucwords($input, $separator));
    }

    /**
     * @param $digitalData
     * @return mixed
     */
    private function cloneDigitalData($digitalData)
    {
        $digitalDataCorrect = clone $digitalData;
        return $digitalDataCorrect->setData([]);
    }

    /**
     * @param $data
     * @return array
     */
    private function filterDigitalData($data)
    {
        return array_filter($data, function ($value) {
            return (trim($value) !== '' || $value != null);
        });
    }
}
