<?php

namespace SM\Sales\Controller\Order;

use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use SM\Sales\Model\OrderItemRepository;
use Magento\Checkout\Model\Session;

/**
 * Class AbstractReorder
 * @package SM\Sales\Controller\Order
 */
abstract class AbstractReorder extends Action
{
    /**
     * @var OrderItemRepository
     */
    protected $orderItemRepository;
    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * AbstractReorder constructor.
     * @param OrderItemRepository $orderItemRepository
     * @param CurrentCustomer $currentCustomer
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param Session $checkoutSession
     */
    public function __construct(
        OrderItemRepository $orderItemRepository,
        CurrentCustomer $currentCustomer,
        Context $context,
        StoreManagerInterface $storeManager,
        Session $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->storeManager = $storeManager;
        $this->currentCustomer = $currentCustomer;
        $this->orderItemRepository = $orderItemRepository;
        parent::__construct($context);
    }
}
