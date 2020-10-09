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

use SM\Notification\Model\Email as Model;

class Email extends AbstractConsumer
{
    const EMAIL_PARAM_ORDER_KEY = 'order';

    /**
     * @var \SM\Notification\Helper\Generate\Email
     */
    protected $helper;

    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    protected $orderRepository;

    /**
     * Email constructor.
     *
     * @param \Magento\Sales\Model\OrderRepository                                $orderRepository
     * @param \SM\Notification\Helper\Generate\Email                              $helper
     * @param \SM\Notification\Helper\CustomerSetting                             $settingHelper
     * @param \Magento\Framework\App\ResourceConnection                           $resourceConnection
     * @param \Magento\Customer\Api\CustomerRepositoryInterface                   $customerRepository
     * @param \Trans\IntegrationNotification\Api\IntegrationNotificationInterface $integrationNotification
     * @param \Magento\Framework\Logger\Monolog|null                              $logger
     */
    public function __construct(
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \SM\Notification\Helper\Generate\Email $helper,
        \SM\Notification\Helper\CustomerSetting $settingHelper,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
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
        $this->helper = $helper;
        $this->orderRepository = $orderRepository;
    }

    public function process(\SM\Notification\Api\Data\Queue\EmailInterface $request)
    {
        try {
            $customer = $this->getCustomer($request->getCustomerId());
            if (!$this->validate($customer->getId(), $request->getEvent(), Model::NOTIFICATION_TYPE)) {
                $this->logError(
                    "Consumer `Email` Error:\n\tCustomer isn't selected email.\n",
                    $request->getData()
                );

                return;
            }

            $template = $this->helper->getEmailTemplateById($request->getTemplateId());
            if ($template) {
                $template->setVars($this->prepareParams($request->getParams()));
                $subject = $request->getSubject() ?: $template->getSubject();
                $result = $this->integration->sendEmail(
                    $customer->getEmail(),
                    $subject,
                    $template->processTemplate()
                );
                $this->logInfo(
                    "Consumer `Email` Success\n",
                    [
                        'result' => $result,
                        'params' => $request->getData(),
                    ]
                );
            } else {
                $this->logError(
                    "Consumer `Email` Error: Can not found email template" . "\n",
                    $request->getData()
                );
            }
        } catch (\Exception $e) {
            $this->reSyncUpdate($request->getId());
            $this->logError(
                "Consumer `Email` Error:\n\t" . $e->getMessage() . "\n",
                $request->getData()
            );
        }
    }

    /**
     * @param $id
     */
    protected function reSyncUpdate($id)
    {
        $this->connection->update(
            \SM\Notification\Model\ResourceModel\CustomerMessage::TABLE_NAME,
            ['email_status' => \SM\Notification\Model\Notification::SYNC_PENDING],
            "id = {$id}"
        );
    }

    /**
     * @return string[]
     */
    protected function getAllowEmailParamModel()
    {
        return [
            self::EMAIL_PARAM_ORDER_KEY,
        ];
    }

    protected function getParamModel($key, $id)
    {
        try {
            switch ($key) {
                case self::EMAIL_PARAM_ORDER_KEY:
                    return $this->orderRepository->get($id);
                default:
                    return $id;
            }
        } catch (\Exception $e) {
            return $id;
        }
    }

    /**
     * @param array $params
     *
     * @return array
     */
    protected function prepareParams($params)
    {
        if (!is_array($params)) {
            return $params;
        }

        $result = [];
        $modelKeys = $this->getAllowEmailParamModel();
        foreach ($params as $key => $value) {
            if (in_array($key, $modelKeys)) {
                $result[$key] = $this->getParamModel($key, $value);
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
