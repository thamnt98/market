<?php
namespace SM\Notification\Block\Customer;

class ListNotification extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \SM\Notification\Model\ResourceModel\CustomerMessage\Collection
     */
    protected $collection = null;

    /**
     * @var \SM\Notification\Model\CustomerMessageRepository
     */
    protected $customerMessageRepo;

    /**
     * @var \SM\Notification\Helper\Data
     */
    protected $helper;

    /**
     * @var array
     */
    protected $eventEnabled = [];

    /**
     * ListNotification constructor.
     *
     * @param \SM\Notification\Helper\Data                         $helper
     * @param \SM\Notification\Model\CustomerMessageRepository     $customerMessageRepo
     * @param \Magento\Framework\View\Element\Template\Context     $context
     * @param \Magento\Customer\Model\Session                      $customerSession
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param array                                                $data
     */
    public function __construct(
        \SM\Notification\Helper\Data $helper,
        \SM\Notification\Model\CustomerMessageRepository $customerMessageRepo,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->customerSession = $customerSession;
        $this->timezone = $timezone;
        $this->customerMessageRepo = $customerMessageRepo;
        $this->helper = $helper;
    }

    /**
     * @return \SM\Notification\Model\ResourceModel\CustomerMessage\Collection
     */
    public function getCollection()
    {
        if (is_null($this->collection)) {
            $this->collection = $this->customerMessageRepo->getCollectionByIds($this->customerSession->getCustomerId());
        }

        return $this->collection;
    }

    /**
     * @return bool
     */
    public function issetParamRequestKey()
    {
        $key = $this->getRequest()->getParam('date');
        if ($key != '' && $key != null) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getTopToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    /**
     * @return string
     */
    public function getPagingHtml()
    {
        return $this->getChildHtml('paging');
    }

    /**
     * @param \SM\Notification\Model\CustomerMessage $item
     *
     * @return string
     */
    public function getItemImageUrl($item)
    {
        return $this->helper->getNotificationImageUrl($item->getData('image'));
    }

    /**
     * @param string $content
     *
     * @return string
     */
    public function convertOrderContent($content)
    {
        preg_match('/ID\/\d+-*\d*/', $content ?? '', $highlight);

        if (count($highlight)) {
            $content = str_replace($highlight[0], '<span class="highlight">' . $highlight[0] . '</span>', $content);
        }

        return $content;
    }

    /**
     * @param \SM\Notification\Model\CustomerMessage $msg
     *
     * @return string
     */
    public function getTitle($msg)
    {
        $params = json_decode($msg->getData('params') ?? '', true);

        return __($msg->getData('title') ?? '', $params['title'] ?? []);
    }

    /**
     * @param \SM\Notification\Model\CustomerMessage $msg
     *
     * @return string
     */
    public function getContent($msg)
    {
        $params = json_decode($msg->getData('params') ?? '', true);

        return __($msg->getData('content') ?? '', $params['content'] ?? []);
    }

    /**
     * @param string $date
     *
     * @return string
     */
    public function convertDate($date)
    {
        try {
            return $this->timezone->date(new \DateTime($date))->format('j M Y H:i:s');
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * @param string $type
     *
     * @return string
     */
    public function convertEventType($type)
    {
        if (!$this->eventEnabled) {
            $this->eventEnabled = $this->helper->getEventEnable();
        }

        return $this->eventEnabled[$type]['name'] ?? $type;
    }

    /**
     * @param \SM\Notification\Model\CustomerMessage $notify
     *
     * @return string
     */
    public function getRedirectUrl($notify)
    {
        return $this->helper->getRedirectUrl($notify->getRedirectType(), $notify->getRedirectId());
    }

    /**
     * @override
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $collection = $this->getCollection();
        /** @var Toolbar $toolbar */
        $toolbar = $this->getChildBlock('notification.list.toolbar');
        if ($toolbar) {
            $toolbar->setCollection($collection);
            $this->setChild('toolbar', $toolbar);
        }

        /** @var \Magento\Theme\Block\Html\Pager $toolbar */
        $paging = $this->getChildBlock('notification.list.pager');
        if ($paging) {
            $limit = $this->getRequest()->getParam($paging->getLimitVarName(), 20);
            $paging->setLimit($limit)->setCollection($collection);
            $this->setChild('paging', $paging);
        }

        if (!$collection->isLoaded()) {
            $collection->load();
        }

        return parent::_beforeToHtml();
    }
}
