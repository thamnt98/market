<?php
/**
 * @category Magento
 * @package SM\Sales\Model
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Sales\Model;

use Exception;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Customer\Model\Session;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Quote\Api\Data\CartItemInterfaceFactory;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\ResourceModel\Quote as QuoteResource;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\Order\ItemRepository;
use SM\Sales\Api\Data\DetailItemDataInterface;
use SM\Sales\Api\Data\ResultDataInterface;
use SM\Sales\Api\Data\ResultDataInterfaceFactory;
use SM\Sales\Api\Data\SubOrderDataInterface;
use SM\Sales\Api\OrderItemRepositoryInterface;

/**
 * Class ItemRepository
 * @package SM\Sales\Model
 */
class OrderItemRepository implements OrderItemRepositoryInterface
{
    /**
     * @var ResultDataInterfaceFactory
     */
    protected $resultDataFactory;
    /**
     * @var ItemRepository
     */
    protected $orderItemRepository;
    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;
    /**
     * @var ParentOrderRepository
     */
    protected $parentOrderRepository;
    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;
    /**
     * @var QuoteResource
     */
    protected $quoteModel;
    /**
     * @var CartItemInterfaceFactory
     */
    protected $cartItemFactory;
    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var Session
     */
    protected $session;

    /**
     * OrderItemRepository constructor.
     * @param ResultDataInterfaceFactory $resultDataFactory
     * @param ItemRepository $orderItemRepository
     * @param DataObjectFactory $dataObjectFactory
     * @param ParentOrderRepository $parentOrderRepository
     * @param QuoteFactory $quoteFactory
     * @param QuoteResource $quoteModel
     * @param CartItemInterfaceFactory $cartItemFactory
     * @param CustomerRepository $customerRepository
     * @param Session $customerSession
     */
    public function __construct(
        ResultDataInterfaceFactory $resultDataFactory,
        ItemRepository $orderItemRepository,
        DataObjectFactory $dataObjectFactory,
        ParentOrderRepository $parentOrderRepository,
        QuoteFactory $quoteFactory,
        QuoteResource $quoteModel,
        CartItemInterfaceFactory $cartItemFactory,
        CustomerRepository $customerRepository,
        Session $customerSession
    ) {
        $this->session = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->cartItemFactory = $cartItemFactory;
        $this->quoteFactory = $quoteFactory;
        $this->quoteModel=$quoteModel;
        $this->parentOrderRepository = $parentOrderRepository;
        $this->resultDataFactory = $resultDataFactory;
        $this->orderItemRepository = $orderItemRepository;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * @param int $cartId
     * @param int $itemId
     * @return ResultDataInterface
     */
    public function reorder($cartId, $itemId)
    {
        /** @var ResultDataInterface $resultData */
        $resultData = $this->resultDataFactory->create();
        try {
            /** @var Quote $quote */
            $quote = $this->quoteFactory->create();
            $this->quoteModel->loadActive($quote, $cartId);

            /** @var Item $item */
            $item = $this->addOrderItem($itemId, $quote);

            if (!is_string($item)) {
                $quote->collectTotals()->save();

                $resultData->setStatus(1);
                $resultData->setMessage(__("\"%1\" has been added to cart.", $item->getName()));
            } else {
                $resultData->setStatus(0);
                $resultData->setMessage(__($item));
            }
        } catch (Exception $e) {
            $resultData->setStatus(0);
            $resultData->setMessage(__($e->getMessage()));
        }

        return $resultData;
    }

    /**
     * @param int $cartId
     * @param int $parentOrderId
     * @return ResultDataInterface
     */
    public function reorderAll($cartId, $parentOrderId)
    {
        /** @var ResultDataInterface $resultData */
        $resultData = $this->resultDataFactory->create();
        try {
            $customerId = $this->session->getCustomerId();
            $parentOrder = $this->parentOrderRepository->getById($customerId, $parentOrderId);

            /** @var Quote $quote */
            $quote = $this->quoteFactory->create();
            $this->quoteModel->loadActive($quote, $cartId);

            /** @var SubOrderDataInterface $subOrder */
            foreach ($parentOrder->getSubOrders() as $subOrder) {
                /** @var DetailItemDataInterface $item */
                if ($subOrder->getStatus() == ParentOrderRepository::STATUS_COMPLETE) {
                    foreach ($subOrder->getItems() as $item) {
                        $this->addOrderItem($item->getItemId(), $quote);
                    }
                }
            }
            $quote->collectTotals()->save();

            $resultData->setStatus(1);
            $resultData->setMessage(__("You have added all products from a completed order to shopping cart."));
        } catch (Exception $e) {
            $resultData->setStatus(0);
            $resultData->setMessage(__($e->getMessage()));
        }

        return $resultData;
    }

    /**
     * Convert order item to quote item
     *
     * @param int $itemId
     * @param Quote $quote
     * @return \Magento\Quote\Model\Quote\Item|string
     */
    public function addOrderItem($itemId, $quote)
    {
        try {
            /** @var Item $orderItem */
            $orderItem = $this->orderItemRepository->get($itemId);

            /** @var DataObject $info */
            $info = $this->dataObjectFactory->create();
            $info->setData($orderItem->getProductOptionByCode('info_buyRequest'));
            $info->setQty($orderItem->getQtyOrdered());

            return $quote->addProduct($orderItem->getProduct(), $info);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
