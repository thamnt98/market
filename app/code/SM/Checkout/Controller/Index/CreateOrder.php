<?php
/**
 * Created by PhpStorm.
 * User: thelightsp
 * Date: 4/22/20
 * Time: 2:40 PM
 */

namespace SM\Checkout\Controller\Index;

use \Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use \Magento\Multishipping\Model\Checkout\Type\Multishipping\State;
use Magento\Framework\Session\SessionManagerInterface;

class CreateOrder extends Action
{
    /**
     * @var \SM\Checkout\Model\Checkout\Type\Multishipping
     */
    protected $multishipping;
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $cartRepository;
    public function __construct(
        Context $context,
        \SM\Checkout\Model\Checkout\Type\Multishipping $multishipping,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository
    ) {
        parent::__construct($context);
        $this->multishipping = $multishipping;
        $this->cartRepository = $cartRepository;
    }

    /**
     * create suborder by mainorder via controllers
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $quoteId = $this->getRequest()->getParam('quoteId');//229
        $orderId = $this->getRequest()->getParam('orderId');//36
        $quote = $this->cartRepository->get($quoteId);
        $orders = $this->multishipping->createSuborders($orderId, $quote);

        echo "<pre>";
        var_dump('success orders: '.$orders->getOrderSuccess());
        var_dump('fail orders: '.$orders->getOrderFail());
        var_dump('==========================================');
        var_dump('==============Fail reasons:===============');
        var_dump('==========================================');
        if ($orders->getOrderFail() > 0) {
            $data = $this->_url->getBaseUrl().'var/log/exception.log';
            $file = file($data);
            for ($i = max(0, count($file) - $orders->getOrderFail()); $i < count($file); $i++) {
                var_dump($file[$i]);
            }
            exit;
        }
    }
}