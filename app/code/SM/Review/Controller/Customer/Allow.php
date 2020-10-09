<?php

namespace SM\Review\Controller\Customer;

use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use SM\Review\Api\Data\CheckResultDataInterface;
use SM\Review\Api\Data\Product\ProductToBeReviewedInterface;
use SM\Review\Api\Data\ToBeReviewedInterface;
use SM\Review\Model\ToBeReviewedRepository;

/**
 * Class Allow
 * @package SM\Review\Controller\Customer
 */
class Allow extends \Magento\Framework\App\Action\Action
{
    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;
    /**
     * @var ToBeReviewedRepository
     */
    protected $toBeReviewedRepository;
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Allow constructor.
     * @param CurrentCustomer $currentCustomer
     * @param ToBeReviewedRepository $toBeReviewedRepository
     * @param PageFactory $resultPageFactory
     * @param Context $context
     */
    public function __construct(
        CurrentCustomer $currentCustomer,
        ToBeReviewedRepository $toBeReviewedRepository,
        PageFactory $resultPageFactory,
        Context $context
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->currentCustomer = $currentCustomer;
        $this->toBeReviewedRepository = $toBeReviewedRepository;
        parent::__construct($context);
    }

    /**
     * @return int|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultPage = $this->resultPageFactory->create();

        $data = $this->getRequest()->getPostValue();
        if (isset($data["product_id"])) {
            $productId = $data["product_id"];
            $customerId = $this->currentCustomer->getCustomerId();
            /** @var CheckResultDataInterface $result */
            $result = $this->toBeReviewedRepository->isReviewAllowed($customerId, $productId);
            if ($result->getIsAllow()) {
                $block = $resultPage->getLayout()
                    ->createBlock(\SM\Review\Block\Product\Button::class)
                    ->setTemplate('SM_Review::pdp/write-button.phtml')
                    ->setOrderId($result->getOrderId())
                    ->toHtml();
                $resultJson->setData($block);
                return $resultJson;
            }
        }
        $resultJson->setData(0);
        return $resultJson;
    }
}
