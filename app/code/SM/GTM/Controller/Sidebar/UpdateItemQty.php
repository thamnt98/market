<?php

namespace SM\GTM\Controller\Sidebar;

use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Checkout\Model\Sidebar;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\Helper\Data;
use Magento\InventoryCatalog\Model\GetStockIdForCurrentWebsite;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Psr\Log\LoggerInterface;
use SM\GTM\Model\ResourceModel\Basket\CollectionFactory;

class UpdateItemQty extends Action
{
    /**
     * @var Sidebar
     */
    protected $sidebar;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Data
     */
    protected $jsonHelper;

    /**
     * @var CustomerCart
     */
    protected $customerCart;

    /**
     * @var CollectionFactory
     */
    protected $basketCollectionFactory;

    /**
     * @var GetStockIdForCurrentWebsite
     */
    protected $getStockIdForCurrentWebsite;

    /**
     * @var GetProductSalableQtyInterface
     */
    protected $getProductSalableQty;

    /**
     * @var float|int
     */
    protected $saleableQty;

    /**
     * @param Context $context
     * @param Sidebar $sidebar
     * @param LoggerInterface $logger
     * @param Data $jsonHelper
     * @param CustomerCart $customerCart
     * @param CollectionFactory $basketCollectionFactory
     * @param GetProductSalableQtyInterface $getProductSalableQty
     * @param GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite
     * @codeCoverageIgnore
     */
    public function __construct(
        Context $context,
        Sidebar $sidebar,
        LoggerInterface $logger,
        Data $jsonHelper,
        CustomerCart $customerCart,
        CollectionFactory $basketCollectionFactory,
        GetProductSalableQtyInterface $getProductSalableQty,
        GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite
    ) {
        $this->sidebar = $sidebar;
        $this->logger = $logger;
        $this->jsonHelper = $jsonHelper;
        $this->customerCart = $customerCart;
        $this->basketCollectionFactory = $basketCollectionFactory;
        $this->getProductSalableQty = $getProductSalableQty;
        $this->getStockIdForCurrentWebsite = $getStockIdForCurrentWebsite;
        parent::__construct($context);
    }

    /**
     * @return Http
     */
    public function execute()
    {
        $itemId = (int)$this->getRequest()->getParam('item_id');
        $itemQty = $this->getRequest()->getParam('item_qty') * 1;

        try {
            $item = $this->customerCart->getQuote()->getItemById($itemId);
            $this->validateItem($item, $itemQty);
            $this->sidebar->updateQuoteItem($itemId, $itemQty);

            if ($this->jsonResponse()->isSuccess()) {
                $message = __(
                    'You updated your shopping cart.'
                );
                //$this->messageManager->addSuccessMessage($message);
            } else {
                $message = __(
                    'We can\'t update your shopping cart.'
                );
                //$this->messageManager->addErrorMessage($message);
            }

            $items = $this->customerCart->getQuote() ? $this->customerCart->getQuote()->getAllVisibleItems() : null;

            if (!$items) {
                $customerId = $this->customerCart->getCustomerSession() ? $this->customerCart->getCustomerSession()->getId() : null;
                if ($customerId) {
                    $basket = $this->basketCollectionFactory->create()->addFieldToFilter('customer_id', $customerId)->getFirstItem();
                    if ($basket->getData()) {
                        $basket->delete();
                    }
                }
            }

            return $this->jsonResponse();
        } catch (LocalizedException $e) {
            return $this->jsonResponse($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return $this->jsonResponse($e->getMessage());
        }
    }

    /**
     * Compile JSON response
     *
     * @param string $error
     * @return Http
     */
    protected function jsonResponse($error = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($this->getResponseData($error))
        );
    }

    /**
     * Compile response data
     *
     * @param string $error
     * @return array
     */
    protected function getResponseData($error = ''): array
    {
        if (empty($error)) {
            $response = [
                'success' => true,
                'qty' => $this->saleableQty
            ];
        } else {
            if ($this->saleableQty || $this->saleableQty = 0) {
                $response = [
                    'success' => false,
                    'error_message' => $error,
                    'qty' => $this->saleableQty
                ];
            } else {
                $response = [
                    'success' => false,
                    'error_message' => $error
                ];
            }

        }
        return $response;
    }

    /**
     * @param string $sku
     * @return float|int
     */
    protected function getSaleableQty(string $sku)
    {
        $stockId = $this->getStockIdForCurrentWebsite->execute();
        try {
            return $this->getProductSalableQty->execute($sku, $stockId);
        } catch (InputException | LocalizedException $e) {
            return 0;
        }
    }

    /**
     * @param $item
     * @param float $itemQty
     * @throws \Exception
     */
    protected function validateItem($item, float $itemQty)
    {
        if (!$item instanceof CartItemInterface) {
            throw new LocalizedException(__("The quote item isn't found. Verify the item and try again."));
        }

        $saleableQty = $this->getSaleableQty($item->getSku());
        $this->saleableQty = $saleableQty;
        if ($saleableQty < $itemQty || $saleableQty == 0) {
            throw new \Exception(__('Sorry, you have reached the maximum number of items for this product.'));
        }
    }
}
