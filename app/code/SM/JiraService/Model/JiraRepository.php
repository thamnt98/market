<?php
/**
 * class JiraRepository
 * @package SM\JiraService\Model
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Dung Nguyen My <dungnm@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\JiraService\Model;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;

class JiraRepository implements \SM\JiraService\Api\JiraRepositoryInterface
{
    const URL_PATH = '/rest/servicedeskapi/request';
    const URL_CREATE_JIRA_CUSTOMER = '/rest/servicedeskapi/customer';
    const URL_GET_JIRA_CUSTOMER = '/rest/servicedeskapi/servicedesk/1/customer';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    private $curl;
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;
    /**
     * @var \SM\JiraService\Api\Data\CreateTicketResponseInterface
     */
    private $createTicketResponse;
    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    private $directoryList;
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;
    /**
     * @var \SM\JiraService\Api\Data\ResponseInterface
     */
    private $responseData;
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory
     */
    private $itemCollectionFactory;
    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    private $orderRepository;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $timezone;
    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    private $resolver;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * JiraRepository constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \SM\JiraService\Api\Data\CreateTicketResponseInterface $createTicketResponse
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \SM\JiraService\Api\Data\ResponseInterface $responseData
     * @param \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $itemCollectionFactory
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Framework\Locale\ResolverInterface $resolver
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \SM\JiraService\Api\Data\CreateTicketResponseInterface $createTicketResponse,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \SM\JiraService\Api\Data\ResponseInterface $responseData,
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $itemCollectionFactory,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Locale\ResolverInterface $resolver,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->curl = $curl;
        $this->jsonHelper = $jsonHelper;
        $this->createTicketResponse = $createTicketResponse;
        $this->directoryList = $directoryList;
        $this->customerRepository = $customerRepository;
        $this->responseData = $responseData;
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->orderRepository = $orderRepository;
        $this->timezone = $timezone;
        $this->resolver = $resolver;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function createTicket($customerId, $typeName, $data)
    {
        try {
            $requestData = $this->generatePostData($typeName, $data, $customerId);
            return $this->process($data, $requestData);
        } catch (\Exception $e) {
            return $this->responseData->setError(true)->setMessage(__('Could not create ticket'));
        }
    }

    /**
     * @param $typeName
     * @param $data
     * @param $customer
     * @return \SM\JiraService\Api\Data\ResponseInterface
     */
    public function createTicketFromEvent($typeName, $data, $customer)
    {
        try {
            $requestData = $this->generatePostData($typeName, $data, $customerId = null, $customer);
            return $this->process($data, $requestData);
        } catch (\Exception $e) {
            return $this->responseData->setError(true)->setMessage(__('Could not create ticket'));
        }
    }

    /**
     * @param $data
     * @param $requestData
     * @return \SM\JiraService\Api\Data\ResponseInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function process($data, $requestData)
    {
        $ticket = $this->send($requestData);
        if (!$ticket->getIsError()) {
            if (array_key_exists('images', $data) && is_array($data['images'])) {
                foreach ($data['images'] as $image) {
                    if (isset($image['file'])) {
                        $this->addAttachment($ticket->getKey(), $image['file']);
                    } else {
                        $this->addAttachment($ticket->getKey(), $image);
                    }
                }
            }
            return $this->responseData->setError(false)->setMessage(__('Created ticket successful'));
        }
        return $this->responseData->setError(true)->setMessage(__('Could not create ticket'));
    }

    /**
     * @param $data
     * @return \SM\JiraService\Api\Data\CreateTicketResponseInterface
     */
    protected function send($data)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/jira_create_ticket.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info("=================Create New Ticket==================");

        $requestUrl = $this->scopeConfig->getValue('sm_jira/ticket/domain') . self::URL_PATH;
        $username = $this->scopeConfig->getValue('sm_jira/account/username');
        $apiToken = $this->scopeConfig->getValue('sm_jira/account/api_token');
        $this->curl->setHeaders(['Content-Type' => 'application/json', 'Accept' => 'application/json']);
        $this->curl->setCredentials($username, $apiToken);
        $this->curl->post($requestUrl, $this->jsonHelper->jsonEncode($data));

        // Get response
        $response = $this->curl->getBody();
        // Return data
        if ($this->curl->getStatus() == '201') {
            $logger->info('Created Ticket', [
                'requestUrl' => $requestUrl,
                'param' => $data,
                'response' => $response
            ]);
            $decodeResponse = $this->jsonHelper->jsonDecode($response);
            $this->createTicketResponse->setId($decodeResponse['issueId'])
                ->setKey($decodeResponse['issueKey'])
                ->setSelfLink($decodeResponse['_links']['self'])
                ->setIsError(false)
                ->setMessage('Created jira ticket successful');
            return $this->createTicketResponse;
        } else {
            $msg = "Can't create jira ticket";
            if ($response != null) {
                $decodeResponse = $this->jsonHelper->jsonDecode($response);
                if (isset($decodeResponse['error']['message'])) {
                    $msg = $decodeResponse['error']['message'];
                }
            }
            $response = $response != null ? $response : ['status' => $this->curl->getStatus()];
            $logger->info("Can't create jira ticket", [
                'requestUrl' => $requestUrl,
                'params' => $data,
                'response' => $response
            ]);
            $this->createTicketResponse
                ->setIsError(true)
                ->setMessage($msg);
            return $this->createTicketResponse;
        }
    }

    /**
     * @param $ticketKey
     * @param $file
     * @return bool
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function addAttachment($ticketKey, $file)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/jira_create_ticket.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info("=================Add attachment==================");

        $url = $this->scopeConfig->getValue('sm_jira/ticket/domain') . "/rest/api/3/issue/{$ticketKey}/attachments";
        $username = $this->scopeConfig->getValue('sm_jira/account/username');
        $apiToken = $this->scopeConfig->getValue('sm_jira/account/api_token');
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => [
                "Accept: application/json",
                "Authorization: Basic " . base64_encode($username . ':' . $apiToken),
                "X-Atlassian-Token: no-check",
                "cache-control: no-cache"
            ],
        ]);

        $path = $this->directoryList->getPath('media') . DIRECTORY_SEPARATOR . 'sm/help/contactus/uploads' . $file;

        if (function_exists('curl_file_create')) {
                $args['file'] = curl_file_create(
                    $path,
                    mime_content_type($path)
                );
                curl_setopt($curl, CURLOPT_POSTFIELDS, $args);
        } else {
                curl_setopt($curl, CURLOPT_POSTFIELDS, [
                    $args['file'] => '@' . $path,
                ]);
        }

        curl_setopt($curl, CURLOPT_POSTFIELDS, $args);
        curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            $logger->info('Error', $err);
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param $typeName
     * @param $data
     * @param $customerId
     * @param null $customer
     * @return array
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function generatePostData($typeName, $data, $customerId = null, $customer = null)
    {
        $serviceDeskId = $this->scopeConfig->getValue('sm_jira/ticket/servicedesk');
        $generalRequest = $this->scopeConfig->getValue('sm_jira/ticket/general');
        $returnRequest = $this->scopeConfig->getValue('sm_jira/ticket/return');

        if (!$customer) {
            $customer = $this->customerRepository->getById($customerId);
        }
        $requestData = ['requestParticipants' => [$this->getJiraCustomerId(
            $customer->getLastname(),
            $customer->getEmail()
        )]];

        $requestData['serviceDeskId'] = $serviceDeskId;

        if ($data['category'] == $this->getReturnRefundId()) {
            $requestData['requestTypeId'] = $returnRequest;
            $requestData['requestFieldValues']['customfield_10035'] = $customer->getEmail();
            $requestData['requestFieldValues']['customfield_10041'] = $customer->getFirstname()
                . ' ' . $customer->getLastname();
            if (array_key_exists('order_increment_id', $data)) {
                $requestData['requestFieldValues']['summary'] = 'Return Request For '
                    . $data['order_increment_id'] . ' by '
                    . $customer->getFirstname() . ' ' . $customer->getLastname();
                // Field Order ID
                $requestData['requestFieldValues']['customfield_10037'] = $data['order_increment_id'];

                // Field Reason
                $requestData['requestFieldValues']['customfield_10047'] = $data['description'];

                // Field Store
                if ($data['is_received'] == 'return') {
                    $requestData['requestFieldValues']['customfield_10048'] = $data['store'];
                } else {
                    $requestData['requestFieldValues']['customfield_10048'] = 'None';
                }

                /** @var \Magento\Sales\Model\ResourceModel\Order\Item\Collection $orderItemCollection */
                $orderItemCollection = $this->itemCollectionFactory->create();
                $orderItemCollection->addFieldToFilter('order_id', $data['order_id']);
                $order = $this->orderRepository->get($data['order_id']);
                $parentId = $order->getData('parent_order');
                $parentOrder = $this->orderRepository->get($parentId);

                //Field Order Detail
                $requestData['requestFieldValues']['customfield_10038'] = $this->getOrderDetail($orderItemCollection);

                //Field Order Reference No
                $requestData['requestFieldValues']['customfield_10045'] = $parentOrder->getIncrementId();

                //Field Return Product Detail
                if ($data['product_ids']) {
                    $requestData['requestFieldValues']['customfield_10046'] = $this->getDetail(
                        $orderItemCollection,
                        $data['product_ids']
                    );
                } else {
                    $requestData['requestFieldValues']['customfield_10046'] = 'None';
                }
                // Field Received
                $requestData['requestFieldValues']['customfield_10054'] = $data['is_received'];
            }
        } elseif ($data['category'] == $this->getMyOrderId()) {
            $requestData['requestTypeId'] = $generalRequest;
            $requestData['requestFieldValues']['summary'] = 'Request Support From '
                . $customer->getFirstname() . ' ' . $customer->getLastname();
            $requestData['requestFieldValues']['customfield_10035'] = $customer->getEmail();
            $requestData['requestFieldValues']['customfield_10036'] = $typeName;

            $requestData['requestFieldValues']['description'] = $data['description'];

            if (array_key_exists('order_increment_id', $data)) {
                // Field Order ID
                $requestData['requestFieldValues']['customfield_10037'] = $data['order_increment_id'];

                /** @var \Magento\Sales\Model\ResourceModel\Order\Item\Collection $orderItemCollection */
                $orderItemCollection = $this->itemCollectionFactory->create();
                $orderItemCollection->addFieldToFilter('order_id', $data['order_id']);

                //Field Order Detail
                $requestData['requestFieldValues']['customfield_10038'] = $this->getOrderDetail($orderItemCollection);

                //Field Order Detail Reported
                $requestData['requestFieldValues']['customfield_10053'] = $this->getDetail(
                    $orderItemCollection,
                    $data['product_ids']
                );
            }
        } else {
            $requestData['requestFieldValues']['summary'] = 'Request Support From '
                . $customer->getFirstname() . ' ' . $customer->getLastname();
            $requestData['requestTypeId'] = $generalRequest;
            $requestData['requestFieldValues']['customfield_10035'] = $customer->getEmail();
            $requestData['requestFieldValues']['customfield_10036'] = $typeName;
            $requestData['requestFieldValues']['description'] = $data['description'];
        }

        return $requestData;
    }

    /**
     *  Get Jira customer ID.
     *
     * @param $name
     * @param $email
     * @return string
     */
    protected function getJiraCustomerId($name, $email)
    {
        $username = $this->scopeConfig->getValue('sm_jira/account/username');
        $requestUrl = $this->scopeConfig->getValue('sm_jira/ticket/domain') . self::URL_CREATE_JIRA_CUSTOMER;
        $apiToken = $this->scopeConfig->getValue('sm_jira/account/api_token');
        $this->curl->setHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ]);
        $this->curl->setCredentials($username, $apiToken);
        $params =
            [
                'displayName' => $name,
                'email'       => $email
            ];
        $this->curl->post($requestUrl, $this->jsonHelper->jsonEncode($params));

        $response = $this->curl->getBody();
        $jiraCustomer = json_decode($response, true);
        if (isset($jiraCustomer['accountId'])) {
            return $jiraCustomer['accountId'];
        } else {
            return $this->getJiraCustomer($email);
        }
    }

    /**
     *  Get exist Jira customer ID.
     *
     * @param $email
     * @return string
     */
    protected function getJiraCustomer($email)
    {
        $requestUrl = $this->scopeConfig->getValue('sm_jira/ticket/domain') . self::URL_GET_JIRA_CUSTOMER;
        $username = $this->scopeConfig->getValue('sm_jira/account/username');
        $apiToken = $this->scopeConfig->getValue('sm_jira/account/api_token');
        $this->curl->setHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X-ExperimentalApi' => 'opt-in'
        ]);
        $this->curl->setCredentials($username, $apiToken);
        $this->curl->get($requestUrl);

        $response = $this->curl->getBody();
        $jiraCustomer = json_decode($response, true);
        $values = $jiraCustomer['values'];
        $accountId = '';
        foreach ($values as $customer) {
            if ($customer['emailAddress'] == $email) {
                $accountId = $customer['accountId'];
                break;
            }
        }
        return $accountId;
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
     * @param $orderCollection
     * @return string
     */
    protected function getOrderDetail($orderCollection)
    {
        $orderDetail = '';
        $index = 1;
        foreach ($orderCollection as $item) {
            $orderDetail .= $index++
                . '. Product SKU: ' . $item->getSku()
                . ', Product Name: ' . $item->getName()
                . ', Quantity: ' . (int)$item->getQtyOrdered() . '   ';
        }
        return $orderDetail;
    }

    /**
     * @param $orderCollection
     * @param $productId
     * @return string
     */
    protected function getDetail($orderCollection, $productId)
    {
        $productIds = explode(',', $productId);
        $content = '';
        $identify = 1;
        foreach ($orderCollection as $item) {
            if (in_array($item->getProductId(), $productIds)) {
                $content .= $identify++ . $this->getContent($item);
            } elseif (in_array($item->getItemId(), $productIds)) {
                $content .= $identify++ . $this->getContent($item);
            }
        }
        return $content;
    }

    protected function getContent($item)
    {
        return
              '. Product SKU: ' . $item->getSku()
            . ', Product Name: ' . $item->getName()
            . ', Quantity: ' . (int)$item->getQtyOrdered() . '   ';
    }
}
