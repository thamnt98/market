<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: July, 08 2020
 * Time: 1:39 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Ui\Component\Form;

use SM\Notification\Model\ResourceModel\Notification as Resource;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \SM\Notification\Model\ResourceModel\Notification\Collection
     */
    protected $collection;

    /**
     * @var array
     */
    protected $loadedData = null;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * DataProvider constructor.
     *
     * @param \Magento\Store\Model\StoreManagerInterface                          $storeManager
     * @param \SM\Notification\Model\ResourceModel\Notification\CollectionFactory $collectionFactory
     * @param string                                                              $name
     * @param string                                                              $primaryFieldName
     * @param string                                                              $requestFieldName
     * @param array                                                               $meta
     * @param array                                                               $data
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \SM\Notification\Model\ResourceModel\Notification\CollectionFactory $collectionFactory,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->storeManager = $storeManager;
    }

    /**
     * @return array|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getData()
    {
        if (is_null($this->loadedData)) {
            $baseUrl = $this->storeManager->getStore()->getBaseUrl();
            /** @var \SM\Notification\Model\Notification $item */
            foreach ($this->getCollection() as $item) {
                $data = $item->getData();
                if (!empty($data['image'])) {
                    $name = explode('/', $data['image']);
                    $url = strpos($data['image'], 'http') === false ? $baseUrl . $data['image'] : $data['image'];
                    $data['image'] = [
                        [
                            'name' => $name[count($name) - 1],
                            'url'  => $url
                        ]
                    ];

                    if (!empty($data[Resource::PUSH_TITLE_ALIAS])) {
                        $data['is_push'] = true;
                    }

                    if (!empty($data[Resource::EMAIL_TEMPLATE_ID_ALIAS])) {
                        $data['is_send_email'] = true;
                    }

                    if (!empty($data[Resource::SMS_CONTENT_ALIAS])) {
                        $data['is_send_sms'] = true;
                    }
                }

                if ($data['admin_type'] &&
                    $data['admin_type'] == \SM\Notification\Model\Source\CustomerType::TYPE_CUSTOMER_SEGMENT
                ) {
                    $data['admin_type'] = \SM\Notification\Model\Source\CustomerType::TYPE_CUSTOMER;
                } elseif ($data['admin_type'] &&
                    $data['admin_type'] == \SM\Notification\Model\Source\CustomerType::TYPE_ALL
                ) {
                    $data['customer_ids'] = null;
                }

                $this->loadedData[$item->getId()] = $data;
            }
        }

        return $this->loadedData;
    }

    /**
     * @param \Magento\Framework\Api\Filter $filter
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        $this->getCollection()->addFieldToFilter(
            'main_table.' . $filter->getField(),
            [$filter->getConditionType() => $filter->getValue()]
        );
    }
}
