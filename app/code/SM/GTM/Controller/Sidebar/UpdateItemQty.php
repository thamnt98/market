<?php

namespace SM\GTM\Controller\Sidebar;

use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Checkout\Model\Sidebar;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\Helper\Data;
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
     * @param Context $context
     * @param Sidebar $sidebar
     * @param LoggerInterface $logger
     * @param Data $jsonHelper
     * @param CustomerCart $customerCart
     * @param CollectionFactory $basketCollectionFactory
     * @codeCoverageIgnore
     */
    public function __construct(
        Context $context,
        Sidebar $sidebar,
        LoggerInterface $logger,
        Data $jsonHelper,
        CustomerCart $customerCart,
        CollectionFactory $basketCollectionFactory
    ) {
        $this->sidebar = $sidebar;
        $this->logger = $logger;
        $this->jsonHelper = $jsonHelper;
        $this->customerCart = $customerCart;
        $this->basketCollectionFactory = $basketCollectionFactory;
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
            $this->sidebar->checkQuoteItem($itemId);
            $this->sidebar->updateQuoteItem($itemId, $itemQty);

            if ($this->jsonResponse()->isSuccess()) {
                $message = __(
                    'You updated your shopping cart.'
                );
                $this->messageManager->addSuccessMessage($message);
            } else {
                $message = __(
                    'We can\'t update your shopping cart.'
                );
                $this->messageManager->addErrorMessage($message);
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
            $this->jsonHelper->jsonEncode($this->sidebar->getResponseData($error))
        );
    }
}
