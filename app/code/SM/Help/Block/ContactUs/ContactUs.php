<?php

/**
 * @category SM
 * @package SM_Help
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author      Chinhvd <chinhvd@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Help\Block\ContactUs;

use Exception;
use Magento\Catalog\Helper\Image;
use Magento\Customer\Model\Session;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\ScopeInterface;
use SM\Help\Api\Data\TopicInterface;
use SM\Help\Model\ResourceModel\Topic\Collection;
use SM\Help\Model\ResourceModel\Topic\CollectionFactory as TopicCollectionFactory;
use SM\Sales\Model\ParentOrderRepository;
use SM\StoreLocator\Model\Store\ResourceModel\Location\CollectionFactory as StoreCollectionFactory;
use SM\Theme\Helper\Data;
use SM\Sales\Api\SubOrderRepositoryInterface;

/**
 * Class ContactUs
 * @package SM\Help\Block\ContactUs
 */
class ContactUs extends \Magento\Framework\View\Element\Template
{

    /**
     * @var StoreCollectionFactory
     */
    protected $storeCollectionFactory;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var TopicCollectionFactory
     */
    protected $topicCollectionFactory = null;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    protected $orders;

    /**
     * @var Data
     */
    protected $imageHelper;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var SubOrderRepositoryInterface
     */
    protected $subOrderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * @var Image
     */
    protected $image;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \SM\Help\Model\Config
     */
    protected $config;

    /**
     * @var SortOrderBuilder
     */
    protected $sortOrderBuilder;

    /**
     * @var \Magento\InventoryApi\Api\SourceRepositoryInterface
     */
    protected $sourceRepository;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * ContactUs constructor.
     * @param \SM\Help\Model\Config $config
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Data $imageHelper
     * @param Context $context
     * @param TopicCollectionFactory $topicCollectionFactory
     * @param Session $customerSession
     * @param OrderRepositoryInterface $orderRepository
     * @param TimezoneInterface $timezone
     * @param Image $image
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreCollectionFactory $storeCollectionFactory
     * @param SortOrderBuilder $sortOrderBuilder
     * @param SubOrderRepositoryInterface $subOrderRepository
     * @param \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \SM\Help\Model\Config $config,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Data $imageHelper,
        Context $context,
        TopicCollectionFactory $topicCollectionFactory,
        Session $customerSession,
        OrderRepositoryInterface $orderRepository,
        TimezoneInterface $timezone,
        Image $image,
        ScopeConfigInterface $scopeConfig,
        StoreCollectionFactory $storeCollectionFactory,
        SortOrderBuilder $sortOrderBuilder,
        SubOrderRepositoryInterface $subOrderRepository,
        \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    ) {
        $this->topicCollectionFactory = $topicCollectionFactory->create();
        $this->customerSession = $customerSession;
        $this->imageHelper = $imageHelper;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->timezone = $timezone;
        $this->image = $image;
        $this->scopeConfig = $scopeConfig;
        $this->storeCollectionFactory = $storeCollectionFactory;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->subOrderRepository = $subOrderRepository;
        parent::__construct($context, $data);
        $this->config = $config;
        $this->sourceRepository = $sourceRepository;
        $this->httpContext = $httpContext;
    }

    /**
     * Golbal Prepare Layout
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _prepareLayout()
    {
        //$this->_addBreadcrumbs();
        $this->pageConfig->getTitle()->set(__("Contact Us"));
        parent::_prepareLayout();
    }

    /**
     * Prepare breadcrumbs
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _addBreadcrumbs()
    {
        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');

        $breadcrumbsBlock->addCrumb(
            'back',
            [
                'label' => __('Back'),
                'title' => __('Go to Help Page'),
                'link' => $this->_storeManager->getStore()->getBaseUrl() . 'help'
            ]
        );
    }

    /**
     * Get Help topics
     * @return Collection
     */
    public function getTopics()
    {
        try {
            return $this->topicCollectionFactory
                ->addStoreFilter()
                ->addVisibilityFilter()
                ->addFieldToFilter(TopicInterface::LEVEL, 1);
        } catch (LocalizedException $e) {
        }
    }

    /**
     * Get Store Location
     * @return \SM\StoreLocator\Model\Store\ResourceModel\Location\Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreLocation()
    {
        $useAllStores = (bool)$this->scopeConfig->getValue(
            'sm_help/refund/allow_all',
            ScopeInterface::SCOPE_STORE
        );
        if (!$useAllStores) {
            $storeIds = $this->scopeConfig->getValue(
                'sm_help/refund/store_codes_allow',
                ScopeInterface::SCOPE_STORE
            );
            $storeIds = explode(',', $storeIds);
            $searchCriteria = $this->searchCriteriaBuilder->addFilter(
                SourceItemInterface::SOURCE_CODE,
                ['in' => $storeIds]
            )
                ->addFilter('enabled', 1)
                ->create();
        } else {
            $searchCriteria = $this->searchCriteriaBuilder->addFilter(
                SourceItemInterface::SOURCE_CODE,
                'default',
                'neq'
            )
                ->addFilter('enabled', 1)
                ->create();
        }
        $sourceData = $this->sourceRepository->getList($searchCriteria);
        return $sourceData->getItems();
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getBackUrl()
    {
        return $this->config->getBaseUrl();
    }

    /**
     * Get Order
     * @return \Magento\Sales\Model\ResourceModel\Order\Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOrder()
    {
        if (!$this->orders) {
            $this->orders = $this->getOrderList();
        }
        return $this->orders;
    }

    /**
     * @return \Magento\Sales\Model\ResourceModel\Order\Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOrderForReturn()
    {
        $status = ParentOrderRepository::STATUS_DELIVERED . ',' . ParentOrderRepository::STATUS_COMPLETE;
        return $this->getOrderList($status);
    }

    /**
     * @param string $status
     * @return \Magento\Sales\Model\ResourceModel\Order\Collection|\SM\Sales\Api\Data\SubOrderSearchResultsInterface
     */
    protected function getOrderList($status = '')
    {
        try {
            $to = date("Y-m-d h:i:s"); // current date
            $from = strtotime('-90 days', strtotime($to));
            $from = date('Y-m-d h:i:s', $from); // 24 hours before
            $sortOrder = $this->sortOrderBuilder->setField(ParentOrderRepository::SORT_LATEST)->setDirection('DESC')->create();
            $customerId = $this->customerSession->getCustomerId();
            $searchCriteriaBuilder = $this->searchCriteriaBuilder
                ->addFilter('created_at', $from, 'gteq')
                ->setPageSize(200)
                ->setSortOrders([$sortOrder]);

            if (!empty($status)) {
                $searchCriteriaBuilder->addFilter('status', $status, 'in');
            }
            $searchCriteria = $searchCriteriaBuilder->create();
            return $this->subOrderRepository->getList($searchCriteria, $customerId);
        } catch (Exception $e) {
        }
    }

    /**
     * @param $image
     * @param null $width
     * @param null $height
     * @return bool|string
     * @throws Exception
     */
    public function getImageResize($image, $width = null, $height = null)
    {
        return $this->imageHelper->getImageResize($image, $width, $height);
    }

    /**
     * Get Store Id
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * Get Format Date
     * @param $order
     * @return string
     * @throws Exception
     */
    public function getFormatDate($order)
    {
        return $this->timezone->date($order)->format('d M Y');
    }

    /**
     * @return string
     */
    public function getMyOrderId()
    {
        return $this->scopeConfig->getValue(
            'sm_help/main_page/contact_us_my_order',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getReturnRefundId()
    {
        return $this->scopeConfig->getValue(
            'sm_help/main_page/contact_us_return_refund',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Checking customer register status
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

}
