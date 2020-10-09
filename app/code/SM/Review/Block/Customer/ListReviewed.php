<?php

namespace SM\Review\Block\Customer;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Block\Account\Dashboard;
use Magento\Customer\Model\Session;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Newsletter\Model\SubscriberFactory;
use SM\Review\Api\ReviewedRepositoryInterface;
use SM\Review\Api\ToBeReviewedRepositoryInterface;

/**
 * Class ListReviewed
 * @package SM\Review\Block\Customer
 */
class ListReviewed extends Dashboard
{
    protected $tab;
    protected $reviewed;
    protected $toBeReviewed;
    /**
     * @var ToBeReviewedRepositoryInterface
     */
    private $toBeReviewedRepository;
    /**
     * @var FilterBuilder
     */
    private $filterBuilder;
    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;
    /**
     * @var FilterGroupBuilder
     */
    private $filterGroupBuilder;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var ReviewedRepositoryInterface
     */
    private $reviewedRepository;

    /**
     * ListReviewed constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param SubscriberFactory $subscriberFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param AccountManagementInterface $customerAccountManagement
     * @param ToBeReviewedRepositoryInterface $toBeReviewedRepository
     * @param ReviewedRepositoryInterface $reviewedRepository
     * @param FilterBuilder $filterBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        SubscriberFactory $subscriberFactory,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $customerAccountManagement,
        ToBeReviewedRepositoryInterface $toBeReviewedRepository,
        ReviewedRepositoryInterface $reviewedRepository,
        FilterBuilder $filterBuilder,
        SortOrderBuilder $sortOrderBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        array $data = []
    ) {
        $this->toBeReviewedRepository = $toBeReviewedRepository;
        $this->filterBuilder = $filterBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->reviewedRepository = $reviewedRepository;
        parent::__construct(
            $context,
            $customerSession,
            $subscriberFactory,
            $customerRepository,
            $customerAccountManagement,
            $data
        );
    }

    protected function _construct()
    {
        $this->tab = $this->_request->getParam('tab', 'to-be-reviewed');
        parent::_construct();
    }

    /**
     * @return $this|Template
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        if ($this->isReviewedTab()){
            $this->reviewed = $this->getListReviewed();
            $collection = $this->reviewedRepository->getOrderCollection();
        } else {
            $this->toBeReviewed = $this->getListToBeReviewed();
            $collection = $this->toBeReviewedRepository->getOrderCollection();
        }
        if ($collection) {

            /** @var \SM\Sales\Block\Customer\Order\Pager $pager */
            $pager = $this->getLayout()->createBlock(
                \SM\Review\Block\Customer\Pager::class,
                'order.pager'
            );
            $pager
                ->setLimit(3)
                ->setShowPerPage(false)
                ->setCollection(
                    $collection
                );
            $this->setChild('pager', $pager);
        }
        return $this;
    }

    public function isReviewedTab()
    {
        return $this->tab == "reviewed";
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @return bool|\SM\Review\Api\Data\ReviewedInterface[]
     */
    public function getListReviewed()
    {
        if (!($customerId = $this->customerSession->getCustomerId())) {
            return false;
        }
        if (!$this->reviewed) {
            $criteria = $this->buildCriteria();
            $this->reviewed = $this->reviewedRepository->getList($criteria, $customerId)->getItems();
        }
        return $this->reviewed;
    }

    /**
     * @return bool|\SM\Review\Api\Data\ToBeReviewedInterface[]
     */
    public function getListToBeReviewed()
    {
        if (!($customerId = $this->customerSession->getCustomerId())) {
            return false;
        }
        if (!$this->toBeReviewed) {
            $criteria = $this->buildCriteria();
            $this->toBeReviewed = $this->toBeReviewedRepository->getList($criteria, $customerId)->getItems();
        }

        return $this->toBeReviewed;
    }

    /**
     * @return \Magento\Framework\Api\SearchCriteria
     */
    public function buildCriteria()
    {
        $sort = $this->getRequest()->getParam('sort', 'desc');
        /* Create $searchCriteria */
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $filters = [];

        $sortOrder = $this->sortOrderBuilder
            ->setField('created_at')
            ->setDirection($sort)
            ->create();
        $searchCriteria->setSortOrders([$sortOrder])
            ->setPageSize(20);
        $key = $this->getRequest()->getParam('key', '');
        if ($key != '' && $key != null) {
            $filterKeyWord = $this->filterBuilder->create();
            $filterKeyWord
                ->setField("key")
                ->setValue($key)
                ->setConditionType("like");
            $filters[] = $this->filterGroupBuilder
                ->addFilter($filterKeyWord)
                ->create();
        }
        if (!empty($filters)) {
            $searchCriteria->setFilterGroups($filters);
        }
        $searchCriteria->setPageSize(3);
        $searchCriteria->setCurrentPage($this->getRequest()->getParam("p", 1));
        return $searchCriteria;
    }

    /**
     * @return bool
     */
    public function issetParamRequestKey()
    {
        $key = $this->getRequest()->getParam('key', '');
        if ($key != '' && $key != null) {
            return true;
        }
        return false;
    }
}
