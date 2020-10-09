<?php

namespace SM\Notification\Block\Customer;

class Toolbar extends \Magento\Framework\View\Element\Template
{
    const FILTER_TAB          = 'tab';
    const FILTER_DATE         = 'date';
    const FILTER_CURRENT_PAGE = 'page';
    const FILTER_SORT         = 'sort';
    const FILTER_CONTENT      = 'query';

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $session;

    /**
     * @var \SM\Notification\Model\ResourceModel\CustomerMessage\Collection
     */
    protected $collection;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \SM\Notification\Model\CustomerMessageRepository
     */
    protected $customerMessageRepo;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * Toolbar constructor.
     *
     * @param \Magento\Framework\Api\SearchCriteriaBuilder         $searchCriteriaBuilder
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \SM\Notification\Model\CustomerMessageRepository     $customerMessageRepo
     * @param \Magento\Framework\View\Element\Template\Context     $context
     * @param \Magento\Customer\Model\Session                      $session
     * @param array                                                $data
     */
    public function __construct(
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \SM\Notification\Model\CustomerMessageRepository $customerMessageRepo,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $session,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->session = $session;
        $this->customerMessageRepo = $customerMessageRepo;
        $this->timezone = $timezone;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @return string
     */
    public function getTab()
    {
        return $this->getRequest()->getParam(self::FILTER_TAB);
    }

    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->getUrl('*/*/*', ['tab' => $this->getTab(), '_current' => true, '_use_rewrite' => true]);
    }

    /**
     * @return int
     */
    public function countUnread()
    {
        if ($this->getTab() && $this->getTab() !== 'all') {
            $this->searchCriteriaBuilder->addFilter('event', $this->getTab());

            return $this->customerMessageRepo->getCountUnread(
                $this->session->getCustomerId(),
                $this->searchCriteriaBuilder->create()
            );
        } else {
            return $this->customerMessageRepo->getCountUnread($this->session->getCustomerId());
        }
    }

    /**
     * @return string
     */
    public function getReadAllUrl()
    {
        return $this->getUrl(
            'notification/request/readAll',
            [
                'type' => $this->getTab()
            ]
        );
    }

    /**
     * @return string|false
     */
    public function getDateFilter()
    {
        if ($date = $this->getRequest()->getParam(self::FILTER_DATE)) {
            try {
                return $this->timezone->convertConfigTimeToUtc($date);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @return string
     */
    public function getDateFilterTxt()
    {
        if ($date = $this->getRequest()->getParam(self::FILTER_DATE)) {
            return date('d M Y', strtotime($date));
        } else {
            return '';
        }

        return '';
    }

    /**
     * @return string
     */
    public function getContentFilter()
    {
        return $this->getRequest()->getParam(self::FILTER_CONTENT);
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return (int)$this->getRequest()->getParam(self::FILTER_CURRENT_PAGE, 1);
    }

    /**
     * @return string
     */
    public function getSort()
    {
        return $this->getRequest()->getParam(self::FILTER_SORT, 'desc');
    }

    /**
     * @param \SM\Notification\Model\ResourceModel\CustomerMessage\Collection $collection
     */
    public function setCollection($collection)
    {
        $messageAlias = \SM\Notification\Model\ResourceModel\CustomerMessage::MESSAGE_JOIN_TABLE_ALIAS;
        if ($this->getTab() && $this->getTab() !== 'all') {
            $collection->getSelect()->where("{$messageAlias}.event = ?", $this->getTab());
        }

        if ($date = $this->getDateFilter()) {
            $collection->getSelect()
                ->where("{$messageAlias}.created_at >= ?", $date)
                ->where("{$messageAlias}.created_at <= DATE_ADD(?, INTERVAL 1 day)", $date);
        }

        $collection->setOrder("{$messageAlias}.created_at");

        $this->collection = $collection;
    }
}
