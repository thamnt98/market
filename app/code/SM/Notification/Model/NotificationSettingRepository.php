<?php

namespace SM\Notification\Model;

use SM\Notification\Api\NotificationSettingRepositoryInterface;
use Magento\Customer\Model\ResourceModel\CustomerFactory;
use Magento\Customer\Model\Customer;

class NotificationSettingRepository implements NotificationSettingRepositoryInterface
{
    /**
     * @var ResourceModel\NotificationSetting\CollectionFactory
     */
    private $notificationSettingCollectionFactory;
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;
    /**
     * @var \Magento\Framework\Controller\Result\Json
     */
    private $json;
    /**
     * @var CustomerFactory
     */
    private $customerFactory;
    /**
     * @var Customer
     */
    private $customer;
    /**
     * @var \SM\Notification\Api\Data\NotificationSettingInterfaceFactory
     */
    private $notificationSettingFactory;
    /**
     * @var \SM\Notification\Api\Data\NotificationSetting\ChildItemInterfaceFactory
     */
    private $childItemInterfaceFactory;
    /**
     * @var \SM\Notification\Api\Data\NotificationSetting\ParentItemInterfaceFactory
     */
    private $parentItemInterfaceFactory;

    public function __construct(
        \SM\Notification\Model\ResourceModel\NotificationSetting\CollectionFactory $notificationSettingCollectionFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Controller\Result\Json $json,
        CustomerFactory $customerFactory,
        Customer $customer,
        \SM\Notification\Api\Data\NotificationSettingInterfaceFactory $notificationSettingFactory,
        \SM\Notification\Api\Data\NotificationSetting\ChildItemInterfaceFactory $childItemInterfaceFactory,
        \SM\Notification\Api\Data\NotificationSetting\ParentItemInterfaceFactory $parentItemInterfaceFactory
    ) {
        $this->notificationSettingCollectionFactory = $notificationSettingCollectionFactory;
        $this->customerRepository = $customerRepository;
        $this->json = $json;
        $this->customerFactory = $customerFactory;
        $this->customer = $customer;
        $this->notificationSettingFactory = $notificationSettingFactory;
        $this->childItemInterfaceFactory = $childItemInterfaceFactory;
        $this->parentItemInterfaceFactory = $parentItemInterfaceFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getNotificationSettingArray($customerId, $area)
    {
        if (is_array($area)) {
            $area = array_merge(['all'], $area);
        } else {
            $area = ['all', $area];
        }

        /** @var \SM\Notification\Model\ResourceModel\NotificationSetting\Collection $notificationSetting */
        $notificationSettingCollection = $this->notificationSettingCollectionFactory->create();
        $notificationSettingCollection->addFieldToFilter('area', ['in' => $area]);
        $defaultValue = $notificationSettingCollection->getData();
        foreach ($defaultValue as $key => $item) {
            $defaultValue[$key]['name'] = __($item['name'])->__toString();
        }
        $customer = $this->customerRepository->getById($customerId);
        $notificationSetting = $customer->getCustomAttribute('notification_setting');
        if ($notificationSetting) {
            if (is_string($notificationSetting->getValue()) && strlen($notificationSetting->getValue()) > 0) {
                $notificationSetting = json_decode($notificationSetting->getValue(), true);
                foreach ($defaultValue as $key => $item) {
                    if (isset($notificationSetting[$item['entity_id']])) {
                        $defaultValue[$key]['default_value'] = $notificationSetting[$item['entity_id']];
                    }
                }
            }
        }

        return $defaultValue;
    }

    /**
     * {@inheritdoc}
     */
    public function getNotificationSetting($customerId, $area)
    {
        $reformatData = [];
        $notificationSetting = $this->getNotificationSettingArray($customerId, $area);
        foreach ($notificationSetting as $item) {
            if ($item['parent_code'] == '') {
                $reformatData[$item['tab']][$item['code']] = [
                    'entity_id' => $item['entity_id'],
                    'name' => $item['name'],
                    'child' => []
                ];
            } else {
                $reformatData[$item['tab']][$item['parent_code']]['child'][] = $item;
            }
        }
        $arrPush = $arrSms = $arrEmail = [];
        foreach ($reformatData as $tab => $setting) {
            foreach ($setting as $tabData) {
                /** @var \SM\Notification\Api\Data\NotificationSetting\ParentItemInterface $parentData */
                $parentData = $this->parentItemInterfaceFactory->create();
                $parentData->setId((int)$tabData['entity_id'])->setName(__($tabData['name'])->__toString());
                $childArr = [];
                foreach ($tabData['child'] as $child) {
                    /** @var \SM\Notification\Api\Data\NotificationSetting\ChildItemInterface $childData */
                    $childData = $this->childItemInterfaceFactory->create();
                    $childData->setId((int)$child['entity_id'])
                        ->setName(__($child['name'])->__toString())
                        ->setValue($child['default_value'])
                        ->setParentCode($child['parent_code']);
                    $childArr[] = $childData;
                }
                $parentData->setChildItem($childArr);
                if ($tab == 'push') {
                    $arrPush[] = $parentData;
                } elseif ($tab == 'email') {
                    $arrEmail[] = $parentData;
                } elseif ($tab == 'sms') {
                    $arrSms[] = $parentData;
                }
            }
        }
        /** @var \SM\Notification\Api\Data\NotificationSettingInterface $notificationSettingData */
        $notificationSettingData = $this->notificationSettingFactory->create();
        $notificationSettingData->setPushNotification($arrPush)
            ->setEmail($arrEmail)
            ->setSms($arrSms);

        return $notificationSettingData;
    }

    /**
     * {@inheritdoc}
     */
    public function updateNotificationSettingData($customerId, $data)
    {
        /** @var \SM\Notification\Model\ResourceModel\NotificationSetting\Collection $notificationSettingCollection */
        $notificationSettingCollection = $this->notificationSettingCollectionFactory->create();
        $defaultValue = $notificationSettingCollection->getData();
        foreach ($defaultValue as $key => $item) {
            $defaultValue[$key]['name'] = __($item['name'])->__toString();
        }
        $customer = $this->customerRepository->getById($customerId);
        $notificationSetting = $customer->getCustomAttribute('notification_setting');
        if ($notificationSetting) {
            if (is_string($notificationSetting->getValue()) && strlen($notificationSetting->getValue()) > 0) {
                $notificationSetting = json_decode($notificationSetting->getValue(), true);
                foreach ($defaultValue as $key => $item) {
                    if (isset($notificationSetting[$item['entity_id']])) {
                        $defaultValue[$key]['default_value'] = $notificationSetting[$item['entity_id']];
                    }
                }
            }
        }
        $notificationData = [];
        try {
            foreach ($defaultValue as $key => $item) {
                if (isset($notificationData[$item['entity_id']])) {
                    continue;
                } elseif ($data->getId() != null && $data->getId() == $item['entity_id']) {
                    $notificationData[$item['entity_id']] = (string)$data->getValue();
                    $duplicate = $this->getDuplicateCode($item['code'], $item['message_type'], $item['entity_id']);
                    foreach ($duplicate as $id) {
                        $notificationData[$id] = $notificationData[$item['entity_id']];
                    }
                } else {
                    $notificationData[$item['entity_id']] = $item['default_value'];
                }
            }
            $customer = $this->customer->load($customerId);
            $customerData = $customer->getDataModel();
            $customerData->setCustomAttribute('notification_setting', json_encode($notificationData));
            $customer->updateData($customerData);
            /** @var \Magento\Customer\Model\ResourceModel\Customer $customerResource */
            $customerResource = $this->customerFactory->create();
            $customerResource->saveAttribute($customer, 'notification_setting');
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function updateNotificationSetting($customerId, $data)
    {
        /** @var \SM\Notification\Model\ResourceModel\NotificationSetting\Collection $notificationSettingCollection */
        $notificationSettingCollection = $this->notificationSettingCollectionFactory->create();
        $defaultValue = $notificationSettingCollection->getData();
        $notificationData = [];
        try {
            foreach ($defaultValue as $item) {
                if (isset($notificationData[$item['entity_id']])) {
                    continue;
                } elseif (isset($data[$item['entity_id']])) {
                    if ($data[$item['entity_id']] === 'on') {
                        $notificationData[$item['entity_id']] = '1';
                    } else {
                        $notificationData[$item['entity_id']] = '0';
                    }

                    $duplicate = $this->getDuplicateCode($item['code'], $item['message_type'], $item['entity_id']);
                    foreach ($duplicate as $id) {
                        $notificationData[$id] = $notificationData[$item['entity_id']];
                    }
                } else {
                    if ($item['parent_code'] != '') {
                        $notificationData[$item['entity_id']] = '0';
                    } else {
                        $notificationData[$item['entity_id']] = $item['default_value'];
                    }
                }
            }
            $customer = $this->customer->load($customerId);
            $customerData = $customer->getDataModel();
            $customerData->setCustomAttribute('notification_setting', json_encode($notificationData));
            $customer->updateData($customerData);
            /** @var \Magento\Customer\Model\ResourceModel\Customer $customerResource */
            $customerResource = $this->customerFactory->create();
            $customerResource->saveAttribute($customer, 'notification_setting');
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param $code
     * @param $messageType
     * @param $excludeId
     *
     * @return array
     */
    protected function getDuplicateCode($code, $messageType, $excludeId)
    {
        /** @var \SM\Notification\Model\ResourceModel\NotificationSetting\Collection $coll */
        $coll = $this->notificationSettingCollectionFactory->create();
        $coll->addFieldToFilter('code', $code)
            ->addFieldToFilter('message_type', $messageType)
            ->addFieldToFilter('entity_id', ['neq' => $excludeId]);

        return $coll->getAllIds();
    }
}
