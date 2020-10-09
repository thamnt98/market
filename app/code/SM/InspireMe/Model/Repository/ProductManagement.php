<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Model\Repository;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class ProductManagement
 * @package SM\InspireMe\Model\Repository
 */
class ProductManagement implements \SM\InspireMe\Api\ProductManagementInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $_formKeyValidator;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var \Magento\Quote\Api\CartManagementInterface
     */
    protected $cartManagement;

    /**
     * @var \Magento\Quote\Api\CartItemRepositoryInterface
     */
    protected $cartItemRepository;
    /**
     * @var \Magento\Quote\Api\Data\CartItemInterfaceFactory
     */
    protected $cartItemFactory;

    /**
     * ProductManagement constructor.
     * @param \Magento\Quote\Api\Data\CartItemInterfaceFactory $cartItemFactory
     * @param \Magento\Quote\Api\CartItemRepositoryInterface $cartItemRepository
     * @param \Magento\Quote\Api\CartManagementInterface $cartManagement
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepository
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Quote\Api\Data\CartItemInterfaceFactory $cartItemFactory,
        \Magento\Quote\Api\CartItemRepositoryInterface $cartItemRepository,
        \Magento\Quote\Api\CartManagementInterface $cartManagement,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        \Magento\Quote\Model\Quote $quote,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->cart = $cart;
        $this->_eventManager = $eventManager;
        $this->_formKeyValidator = $formKeyValidator;
        $this->serializer = $serializer;
        $this->messageManager = $messageManager;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->quote = $quote;
        $this->cartRepository = $cartRepository;
        $this->cartManagement = $cartManagement;
        $this->cartItemRepository = $cartItemRepository;
        $this->cartItemFactory = $cartItemFactory;
    }

    /**
     * Initialize product instance from request data
     *
     * @param int $productId
     * @return bool|\Magento\Catalog\Api\Data\ProductInterface
     * @throws NoSuchEntityException
     */
    protected function _initProduct($productId)
    {
        $storeId = $this->storeManager->getStore()->getId();
        try {
            return $this->productRepository->getById($productId, false, $storeId);
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function addSelectedToCart($cartId, $products, $formKey = null)
    {
        $errorMessages = [];

        /** @var \Magento\Quote\Api\Data\CartItemInterface $cartItem */
        $cartItem = $this->cartItemFactory->create();

        foreach ($products as $item) {
            try {
                $product = $this->_initProduct($item['product_id']);
                $cartItem
                    ->setSku($product->getSku())
                    ->setQty($item['product_qty'])
                    ->setQuoteId($cartId);

                try {
                    $this->cartItemRepository->save($cartItem);
                } catch (\Exception $e) {
                    $errorMessages[] = __('Product %1: ', $cartItem->getSku()) . $e->getMessage();
                }
            } catch (NoSuchEntityException $e) {
                $errorMessages[] = __('Product with ID %1 does not exist', $item['product_id']);
            }
        }

        if ($errorMessages !== []) {
            $throwMessage = '';
            foreach ($errorMessages as $message) {
                $this->messageManager->addErrorMessage($message);
                $throwMessage .= $message . "\n";
            }

            throw new LocalizedException(new \Magento\Framework\Phrase($throwMessage));
        } else {
            $message = __('All selected products has been added to cart!');
            $this->messageManager->addSuccessMessage($message);
        }

        return true;
    }
}
