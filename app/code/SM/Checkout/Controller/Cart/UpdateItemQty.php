<?php

namespace SM\Checkout\Controller\Cart;

use Magento\Checkout\Block\Cart\Item\Renderer;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Sidebar;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\Helper\Data;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Psr\Log\LoggerInterface;

class UpdateItemQty extends Action implements HttpPostActionInterface
{
    /**
     * @var Cart
     */
    private $cart;

    /**
     * @var false|\Magento\Quote\Model\Quote\Item
     */
    private $currentItem;

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\Item
     */
    private $itemResourceModel;

    /**
     * @var \Magento\Quote\Model\Quote\ItemFactory
     */
    private $itemFactory;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var ResolverInterface
     */
    protected $resolver;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var Data
     */
    private $jsonHelper;

    public function __construct(
        Context $context,
        Sidebar $sidebar,
        LoggerInterface $logger,
        Data $jsonHelper,
        Cart $cart,
        \Magento\Quote\Model\Quote\ItemFactory $itemFactory,
        \Magento\Quote\Model\ResourceModel\Quote\Item $itemResourceModel,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        ResolverInterface $resolver
    ) {
        $this->cart = $cart;
        $this->itemFactory = $itemFactory;
        $this->itemResourceModel = $itemResourceModel;
        $this->quoteRepository = $quoteRepository;
        $this->resolver = $resolver;
        $this->logger = $logger;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context);
    }

    public function execute()
    {
        return $this->updateItem();
    }

    public function updateItem()
    {
        $itemId = (int)$this->getRequest()->getParam('item_id');
        $itemQty = $this->getRequest()->getParam('item_qty') * 1;

        try {
            $this->checkQuoteItem($itemId, $itemQty);
            $this->updateQuoteItem($itemId, $itemQty);
            return $this->jsonResponse();
        } catch (LocalizedException $e) {
            return $this->jsonResponse($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return $this->jsonResponse($e->getMessage());
        }
    }

    /**
     * Check if required quote item exist
     *
     * @param int $itemId
     * @throws LocalizedException
     * @return $this
     */
    protected function checkQuoteItem($itemId, $itemQty)
    {
        $this->currentItem = $item = $this->cart->getQuote()->getItemById($itemId);
        $this->currentItem->setQty($itemQty);
        if (!$item instanceof CartItemInterface) {
            throw new LocalizedException(__("The quote item isn't found. Verify the item and try again."));
        }


        return $this;
    }

    /**
     * Update quote item
     *
     * @param int $itemId
     * @param int $itemQty
     * @throws LocalizedException
     * @return $this
     */
    public function updateQuoteItem($itemId, $itemQty)
    {
        $itemData = [$itemId => ['qty' => $this->normalize($itemQty)]];
        $cart = $this->cart->updateItems($itemData);
        $this->quoteRepository->save($cart->getQuote());
        return $this;
    }

    /**
     * Apply normalization filter to item qty value
     *
     * @param int $itemQty
     * @return int|array
     */
    protected function normalize($itemQty)
    {
        if ($itemQty) {
            $filter = new \Zend_Filter_LocalizedToNormalized(
                ['locale' => $this->resolver->getLocale()]
            );
            return $filter->filter($itemQty);
        }
        return $itemQty;
    }

    /**
     * Return row total html
     *
     * @return string
     */
    public function getRowTotalHtml(): string
    {
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);

        /** @var Renderer $block */
        return $resultPage->getLayout()
            ->createBlock('\Magento\Weee\Block\Item\Price\Renderer')
            ->setTemplate('Magento_Weee::item/price/row.phtml')
            ->setItem($this->currentItem)
            ->toHtml();
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
    protected function getResponseData($error = '')
    {
        if (empty($error)) {
            $response = [
                'success' => true,
                'row_total' => $this->getRowTotalHtml()
            ];
        } else {
            $response = [
                'success' => false,
                'error_message' => $error
            ];
        }
        return $response;
    }
}
