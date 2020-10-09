<?php

namespace SM\Sales\Controller\Ajax;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use SM\Sales\Model\ParentOrderRepository;

/**
 * Class ReorderQuickly
 * @package SM\Sales\Controller\Order
 */
class ReorderQuickly extends Action
{
    const CONTENT_TEMPLATE = "SM_Sales::widget/reorder-quickly-content.phtml";

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var ParentOrderRepository
     */
    protected $parentOrderRepository;

    /**
     * ReorderQuickly constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ParentOrderRepository $parentOrderRepository
     * @param Session $customerSession
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ParentOrderRepository $parentOrderRepository,
        Session $customerSession
    ) {
        $this->customerSession = $customerSession;
        $this->parentOrderRepository = $parentOrderRepository;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        if ($this->customerSession->isLoggedIn()) {
            $resultPage = $this->resultPageFactory->create();

            try {
                $orders = $this->parentOrderRepository->getListReorderQuickly($this->customerSession->getCustomerId());
            } catch (NoSuchEntityException $e) {
                $resultJson->setData([
                    "status" => 0,
                    "message" => $e->getMessage()
                ]);
                return $resultJson;
            }

            /** @var \SM\Sales\Block\Order\ReorderQuicklyContent $reorderQuicklyBlock */
            $reorderQuicklyBlock = $resultPage
                ->getLayout()
                ->createBlock(\SM\Sales\Block\Order\ReorderQuicklyContent::class);
            $reorderQuicklyBlock
                ->setOrders($orders)
                ->setTemplate(self::CONTENT_TEMPLATE);

            if (count($orders)) {
                $resultJson->setData([
                    "status" => 1,
                    "block" => $reorderQuicklyBlock->toHtml()
                ]);
            } else {
                $resultJson->setData([
                    "status" => 0,
                    "message" => __("List complete physical order is empty")
                ]);
            }
        } else {
            $resultJson->setData([
                "status" => 0,
                "message" => __("Login is required")
            ]);
        }
        return $resultJson;
    }
}
