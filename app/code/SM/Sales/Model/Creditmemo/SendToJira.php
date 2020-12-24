<?php
/**
 * Class SendToJira
 * @package SM\Sales\Model\Creditmemo
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Sales\Model\Creditmemo;

use SM\Sales\Model\Order\IsPaymentMethod;

class SendToJira
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \SM\JiraService\Model\JiraRepository
     */
    private $jiraRepository;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Creditmemo
     */
    private $creditmemoResource;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    private $customer;

    /**
     * SendToJira constructor.
     * @param \SM\JiraService\Model\JiraRepository $jiraRepository
     * @param \Magento\Sales\Model\ResourceModel\Order\Creditmemo $creditmemoResource
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \SM\JiraService\Model\JiraRepository $jiraRepository,
        \Magento\Sales\Model\ResourceModel\Order\Creditmemo $creditmemoResource,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->jiraRepository = $jiraRepository;
        $this->creditmemoResource = $creditmemoResource;
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param $params
     * @return bool
     */
    public function send($params): bool
    {
        try {
            $data = $this->prepareJiraData($params);
            if (empty($data)) {
                return false;
            }
            $ticket = $this->jiraRepository->send($data);
            if (!$ticket->getIsError()) {
                $this->updateCreditmemo($params[RequestFormData::CREDITMEMO_ID_KEY]);
            }
            return true;
        } catch (\Exception $e) {
            $this->logger->critical(json_encode($e));
            return false;
        }
    }

    /**
     * @param $creditMemoId
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function updateCreditmemo($creditMemoId)
    {
        $table = $this->creditmemoResource->getMainTable();
        $connection = $this->creditmemoResource->getConnection();
        $connection->update($table, array('creditmemo_status' => 2), 'entity_id=' . $creditMemoId . '');
    }

    /**
     * @param $data
     * @return array
     */
    protected function prepareJiraData($data): array
    {
        $requestData = [];
        $creditCardType = IsPaymentMethod::isCard($data[RequestFormData::PAYMENT_METHOD_KEY]);
        if ($this->validateParams($data, $creditCardType)) {
            $requestData = [
                'serviceDeskId' => $this->scopeConfig->getValue('sm_jira/ticket/servicedesk'),
                'requestTypeId' => $this->scopeConfig->getValue('sm_jira/ticket/refund'),
                'requestParticipants' => [
                    $this->jiraRepository->getJiraCustomerId(
                        $this->getCustomer()->getLastname(),
                        $this->getCustomer()->getEmail()
                    )
                ]
            ];

            if ($creditCardType) {
                $summary = __(
                    'Request a refund to credit card for order %1',
                    $data[RequestFormData::ORDER_REFERENCE_NUMBER_KEY]
                );
            } else {
                $summary = __(
                    'Request a refund to the virtual account for order %1',
                    $data[RequestFormData::ORDER_REFERENCE_NUMBER_KEY]
                );
            }

            $requestData['requestFieldValues'] = [
                'summary' => $summary,
                'customfield_10037' =>  $data[RequestFormData::ORDER_REFERENCE_NUMBER_KEY],
                'customfield_10044' =>  $data[RequestFormData::PAYMENT_NUMBER_KEY],
                'customfield_10061' => (int) $data[RequestFormData::TOTAL_REFUND_KEY],
                'customfield_10043' => $data[RequestFormData::BANK_KEY],
                'customfield_10060' => $data[RequestFormData::ACCOUNT_NAME_KEY],
                'customfield_10051' => (int) $data[RequestFormData::ACCOUNT_KEY],
            ];
        }

        return $requestData;
    }

    /**
     * @param array $params
     * @param $creditCardType
     * @return bool|\Magento\Framework\Controller\ResultInterface
     */
    protected function validateParams(array $params, $creditCardType)
    {
        if (!$creditCardType) {
            foreach ($params as $param) {
                if (empty($param)) {
                    return false;
                }
            }
        } else {
            unset($params[RequestFormData::BANK_KEY]);
            unset($params[RequestFormData::ACCOUNT_NAME_KEY]);
            unset($params[RequestFormData::ACCOUNT_KEY]);
            foreach ($params as $param) {
                if (empty($param)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     */
    public function setCustomer(\Magento\Customer\Model\Customer $customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer(): \Magento\Customer\Model\Customer
    {
        return $this->customer;
    }
}
