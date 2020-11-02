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
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Store\Model\ScopeInterface;
use SM\Help\Model\ResourceModel\Topic\CollectionFactory as TopicCollectionFactory;
use SM\Help\Api\Data\TopicInterface;
use SM\Help\Model\ResourceModel\Topic\Collection;
use Magento\Sales\Api\OrderRepositoryInterface;
use SM\Help\Model\Topic;
use SM\Theme\Helper\Data;
use SM\StoreLocator\Model\Store\ResourceModel\Location\CollectionFactory as StoreCollectionFactory;

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
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;

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
     * Constructor
     *
     * @param \SM\Help\Model\Config $config
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Data $imageHelper
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param Context $context
     * @param TopicCollectionFactory $topicCollectionFactory
     * @param Session $customerSession
     * @param OrderRepositoryInterface $orderRepository
     * @param TimezoneInterface $timezone
     * @param Image $image
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreCollectionFactory $storeCollectionFactory
     * @param array $data
     */
    public function __construct(
        \SM\Help\Model\Config $config,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Data $imageHelper,
        OrderCollectionFactory $orderCollectionFactory,
        Context $context,
        TopicCollectionFactory $topicCollectionFactory,
        Session $customerSession,
        OrderRepositoryInterface $orderRepository,
        TimezoneInterface $timezone,
        Image $image,
        ScopeConfigInterface $scopeConfig,
        StoreCollectionFactory $storeCollectionFactory,
        array $data = []
    ) {
        $this->topicCollectionFactory = $topicCollectionFactory->create();
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->customerSession        = $customerSession;
        $this->imageHelper            = $imageHelper;
        $this->orderRepository        = $orderRepository;
        $this->searchCriteriaBuilder  = $searchCriteriaBuilder;
        $this->timezone               = $timezone;
        $this->image                  = $image;
        $this->scopeConfig            = $scopeConfig;
        $this->storeCollectionFactory = $storeCollectionFactory;
        parent::__construct($context, $data);
        $this->config = $config;
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
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
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
        return $this->storeCollectionFactory->create();
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
        try {
            $to = date("Y-m-d h:i:s"); // current date
            $from = strtotime('-90 days', strtotime($to));
            $from = date('Y-m-d h:i:s', $from); // 24 hours before

            $customerId = $this->customerSession->getCustomerId();
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('customer_id', $customerId, 'in')
                ->addFilter('is_parent', '0', 'in')
                ->addFilter('created_at', $from, 'gteq')
                ->setPageSize(10)
                ->create();

            if (!$this->orders) {
                $this->orders = $this->orderRepository->getList($searchCriteria);
            }
            return $this->orders;
        } catch (Exception $e) {
        }
    }

    /**
     * @param $product
     * @param $width
     * @param $height
     * @return bool|string
     * @throws Exception
     */
    public function getImageResize($product, $width = null, $height = null)
    {
        return $this->image->init($product, 'cart_page_product_thumbnail')
            ->setImageFile($product->getImage())
            ->resize($width, $height)
            ->getUrl();
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
}
