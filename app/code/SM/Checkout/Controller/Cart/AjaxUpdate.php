<?php
/**
 * Created by PhpStorm.
 * User: thelightsp
 * Date: 4/6/20
 * Time: 10:32 AM
 */

namespace SM\Checkout\Controller\Cart;

use Magento\Checkout\Model\Sidebar;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\Helper\Data;
use Magento\Checkout\Model\Cart;

class AjaxUpdate extends Action
{
    /**
     * @var Sidebar
     */
    protected $sidebar;

    /**
     * @var Data
     */
    protected $jsonHelper;

    /**
     * @var Cart
     */
    protected $cart;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;
    /**
     * AjaxUpdate constructor.
     * @param Context $context
     * @param Cart $cart
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param Sidebar $sidebar
     * @param Data $jsonHelper
     */
    public function __construct(
        Context $context,
        Cart $cart,
        \Magento\Checkout\Model\Session $checkoutSession,
        Sidebar $sidebar,
        Data $jsonHelper
    ) {
        $this->sidebar = $sidebar;
        $this->jsonHelper = $jsonHelper;
        $this->checkoutSession = $checkoutSession;
        $this->cart = $cart;
        parent::__construct($context);
    }

    /**
     * @return Http|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $itemId = (int)$this->getRequest()->getParam('item_id');
        $itemQty = (int)$this->getRequest()->getParam('item_qty');

        try {
            $this->updateQuoteItem($itemId, $itemQty);
            return $this->jsonResponse();
        } catch (LocalizedException $e) {
            return $this->jsonResponse($e->getMessage());
        } catch (\Exception $e) {
            return $this->jsonResponse($e->getMessage());
        }
    }

    /**
     * @param $itemId
     * @param $itemQty
     * @throws LocalizedException
     */
    public function updateQuoteItem($itemId, $itemQty)
    {
        $itemData = [$itemId => ['qty' => $itemQty]];
        $this->cart->updateItems($itemData)->save();
    }

    /**
     * @return \Magento\Quote\Model\Quote
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getQuote()
    {
        return $this->checkoutSession->getQuote();
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