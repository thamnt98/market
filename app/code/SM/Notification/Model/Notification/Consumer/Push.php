<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: June, 04 2020
 * Time: 11:13 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Model\Notification\Consumer;

use SM\Notification\Model\Push as Model;

class Push extends AbstractConsumer
{
    /**
     * @var \SM\Customer\Model\CustomerDeviceRepository
     */
    protected $customerDeviceRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * Push constructor.
     *
     * @param \SM\Notification\Helper\CustomerSetting                             $settingHelper
     * @param \Magento\Framework\App\ResourceConnection                           $resourceConnection
     * @param \Magento\Framework\Api\SearchCriteriaBuilder                        $searchCriteriaBuilder
     * @param \SM\Customer\Model\CustomerDeviceRepository                         $customerDeviceRepository
     * @param \Magento\Customer\Api\CustomerRepositoryInterface                   $customerRepository
     * @param \Trans\IntegrationNotification\Api\IntegrationNotificationInterface $integrationNotification
     * @param \Magento\Framework\Logger\Monolog|null                              $logger
     */
    public function __construct(
        \SM\Notification\Helper\CustomerSetting $settingHelper,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \SM\Customer\Model\CustomerDeviceRepository $customerDeviceRepository,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Trans\IntegrationNotification\Api\IntegrationNotificationInterface $integrationNotification,
        \Magento\Framework\Logger\Monolog $logger = null
    ) {
        parent::__construct(
            $settingHelper,
            $resourceConnection,
            $customerRepository,
            $integrationNotification,
            $logger
        );
        $this->customerDeviceRepository = $customerDeviceRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    public function process(\SM\Notification\Api\Data\Queue\PushDeviceInterface $request)
    {
        try {
            if ($customer = $this->getCustomer($request->getCustomerId())) {
                if ($this->validate($customer->getId(), $request->getEvent(), Model::NOTIFICATION_TYPE)) {
                    $deviceTokens = $this->getDeviceTokens($customer->getId());
                    if (!empty($deviceTokens)) {
                        $result = $this->integration->pushNotif(
                            $deviceTokens,
                            $request->getTitle(),
                            $request->getContent()
                        );
                        $this->logInfo(
                            "Consumer `Push` Success\n",
                            [
                                'params' => $request->getData(),
                                'result' => $result
                            ]
                        );
                    } else {
                        $this->logError(
                            "Consumer `Push` Error:\n\tDevices are empty.\n",
                            $request->getData()
                        );
                    }
                } else {
                    $this->logError(
                        "Consumer `Push` Error:\n\tCustomer isn't selected push.\n",
                        $request->getData()
                    );
                }
            }
        } catch (\Exception $e) {
            $this->reSyncUpdate($request->getId());
            $this->logError(
                "Consumer `Push` Error:\n\t" . $e->getMessage() . "\n",
                $request->getData()
            );
        }
    }

    /**
     * @param $customerId
     *
     * @return array
     */
    protected function getDeviceTokens($customerId)
    {
        $result = [];
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('customer_id', $customerId)->create();
        $devices = $this->customerDeviceRepository->getList($searchCriteria)->getItems();

        foreach ($devices as $device) {
            $result[] = $device->getToken();
        }

        return $result;
    }

    /**
     * @param int $id
     */
    protected function reSyncUpdate($id)
    {
        $this->connection->update(
            \SM\Notification\Model\ResourceModel\CustomerMessage::TABLE_NAME,
            ['push_status' => \SM\Notification\Model\Notification::SYNC_PENDING],
            "id = {$id}"
        );
    }
}
