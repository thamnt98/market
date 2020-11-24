<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: September, 07 2020
 * Time: 6:51 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Model;

class CustomerMessage extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \SM\Notification\Api\Data\CustomerMessageInterfaceFactory
     */
    protected $dataModelFactory;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var \SM\Notification\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * CustomerMessage constructor.
     *
     * @param \SM\Notification\Helper\Data                                 $helper
     * @param \SM\Notification\Api\Data\CustomerMessageInterfaceFactory    $dataModelFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface         $timezone
     * @param \Magento\Framework\Api\DataObjectHelper                      $dataObjectHelper
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        \SM\Notification\Helper\Data $helper,
        \SM\Notification\Api\Data\CustomerMessageInterfaceFactory $dataModelFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->dataModelFactory = $dataModelFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->helper = $helper;
        $this->timezone = $timezone;
    }

    /**
     * Initialize resource model
     */
    public function _construct()
    {
        $this->_init(\SM\Notification\Model\ResourceModel\CustomerMessage::class);
    }

    /**
     * @return \SM\Notification\Api\Data\CustomerMessageInterface
     */
    public function getDataModel()
    {
        /** @var \SM\Notification\Api\Data\CustomerMessageInterface $dataModel */
        $dataModel = $this->dataModelFactory->create();
        $data = $this->getData();

        $params = $this->getData('params');
        if (!is_array($params)) {
            $params = json_decode($params, true);
        }

        $data['event_label'] = $this->helper->getEventTitle($this->getData('sub_event') ?? $this->getData('event'));
        $data['title'] = __($data['title'] ?? '', $params['title'] ?? [])->__toString();
        $data['content'] = __($data['content'] ?? '', $params['content'] ?? [])->__toString();
        $data['image'] = $this->helper->getNotificationImageUrl($data['image'] ?? '');

        preg_match('/ID\/\d+-*\d*/', $data['title'], $highlightTitle);
        preg_match('/ID\/\d+-*\d*/', $data['content'], $highlightContent);
        $data['highlight_title'] = $highlightTitle[0] ?? '';
        $data['highlight_content'] = $highlightContent[0] ?? '';
        $data[\SM\Notification\Api\Data\CustomerMessageInterface::REDIRECT_URL_KEY] = $this->helper->getRedirectUrl(
            $this->getData(\SM\Notification\Api\Data\CustomerMessageInterface::REDIRECT_TYPE_KEY),
            $this->getData(\SM\Notification\Api\Data\CustomerMessageInterface::REDIRECT_ID_KEY)
        );

        try {
            if (!empty($data['created_at'])) {
                $data['created_at'] = $this->timezone->date(
                    new \DateTime($data['created_at'])
                )->format('j M Y H:i:s');
            }
        } catch (\Exception $e) {
        }

        $this->dataObjectHelper->populateWithArray(
            $dataModel,
            $data,
            \SM\Notification\Api\Data\CustomerMessageInterface::class
        );

        return $dataModel;
    }
}
