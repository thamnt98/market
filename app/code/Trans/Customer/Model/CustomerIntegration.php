<?php
/**
 * @category Trans
 * @package  Trans_Customer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Customer\Model;

use Trans\Core\Helper\Customer;
use Trans\Customer\Api\CustomerIntegrationInterface;
use Trans\Integration\Logger\Logger;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Api\Data\CustomerInterface;
use Trans\Customer\Api\Data\CustomerIntegrationResponseInterfaceFactory;
use Trans\Customer\Api\Data\CustomerIntegrationValidationsInterface;

use Trans\IntegrationCustomer\Api\IntegrationCustomerCentralRepositoryInterface;

/**
 * Class CustomerIntegration
 * @package Trans\Customer\Model
 */
class CustomerIntegration implements CustomerIntegrationInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Trans\Integration\Logger\Logger
     */
    protected $logger;

    /**
     * @var Json
     */
    protected $json;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var CustomerInterfaceFactory
     */
    protected $customerInterface;

    /**
     * @var AccountManagementInterface
     */
    protected $accountManagementInterface;

    /**
     * @var CustomerIntegrationResponseInterface
     */
    protected $response;

    /**
     * @var ValidationsInterface
     */
    protected $validates;

    /**
     * @var Customer
     */
    protected $customerHelper;

    /**
     * @var IntegrationCustomerCentralRepositoryInterface
     */
    protected $customerCentralRepository;

    /**
     * CustomerIntegration constructor.
     * @param RequestInterface $request
     * @param Logger $logger
     * @param Json $json
     * @param CustomerInterfaceFactory $customerInterface
     * @param CustomerFactory $customerFactory
     * @param AccountManagementInterface $accountManagementInterface
     * @param CustomerIntegrationResponseInterfaceFactory $response
     * @param CustomerIntegrationValidationsInterface $validates
     * @param Customer $customerHelper
     * @param IntegrationCustomerCentralRepositoryInterface $customerCentralRepository
     */
    public function __construct(
        RequestInterface $request,
        Logger $logger,
        Json $json,
        CustomerInterfaceFactory $customerInterface,
        CustomerFactory $customerFactory,
        AccountManagementInterface $accountManagementInterface,
        CustomerIntegrationResponseInterfaceFactory $response,
        CustomerIntegrationValidationsInterface $validates,
        Customer $customerHelper,
        IntegrationCustomerCentralRepositoryInterface $customerCentralRepository
    )
    {
        $this->request = $request;
        $this->logger = $logger;
        $this->json = $json;
        $this->customerInterface = $customerInterface;
        $this->customerFactory = $customerFactory;
        $this->accountManagementInterface = $accountManagementInterface;
        $this->response = $response;
        $this->validates = $validates;
        $this->customerHelper = $customerHelper;
        $this->customerCentralRepository = $customerCentralRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function CreateNewCustomer()
    {
        $param = $this->request->getContent();
        $fieldname = CustomerIntegrationInterface::REQUIRED_FIELD_1;

        $data = $this->json->unserialize($param);
        try {
            $this->validates->fields($data, $fieldname);
        } catch (\Exception $ex) {
            return $this->getResponse($ex->getMessage());
        }

        try {
            $result = [];
            $i = 0;
            foreach ($data['data'] as $row) {
                $result[$i] = $this->setCustomer($row);
                $i++;
            }
        } catch (\Exception $ex) {
            return $this->getResponse($ex->getMessage());
        }

        return $this->getResponse("success", $result);
    }

    /**
     * @param array $param
     * @return array
     * @throws StateException
     */
    protected function setCustomer($param = [])
    {
        if (!is_array($param)) {
            throw new StateException(
                __(self::MSG_ERROR_JSON_ARRAY)
            );
        }
        $result = $param;
        $result['status'] = 0;
        $customer = $this->customerInterface->create();
        $customer->setStoreId(self::DEFAULT_STORE_ID);
        $customer->setWebsiteId(self::DEFAULT_STORE_ID);
        $custData = [];
        try {
            //Create Customer
            if (isset($param['customer_name']) && !empty($param['customer_name'])) {
                $custData = $this->customerHelper->generateFirstnameLastname($param['customer_name']);
            }
            if (isset($custData['firstname'])) {
                $customer->setFirstname($custData['firstname']);
            }
            if (isset($custData['lastname'])) {
                $customer->setLastname($custData['lastname']);
            }

            if (isset($param['customer_email'])) {
                $customer->setEmail($param['customer_email']);
            }

            if (isset($param['customer_phone']) && !empty($param['customer_phone'])) {
                $customer->setCustomAttribute('telephone', $param['customer_phone']);
            }
            $customerData = false;
            if (isset($param['customer_password_hash']) && !empty($param['customer_password_hash'])) {
                $customerData = $this->createCustomerSavePassword($customer, $param['customer_password_hash'], 1);
            } else {
                if (isset($param['customer_password']) && !empty($param['customer_password'])) {
                    $customerData = $this->createCustomerSavePassword($customer, $param['customer_password']);
                }
            }
            //Save Map Central ID Customer ID
            if (!$customerData) {
                $query['magento_customer_id'] = $customerData->getId();
                $query['central_id'] = $param['central_id'];
                $this->customerCentralRepository->saveData($query);
            }

        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            $result['status'] = 2;
            return $result;
        }
        $result['error'] = null;
        $result['status'] = 1;
        return $result;
    }

    /**
     * @param CustomerInterface $customer
     * @param $password
     * @param int $hash
     * @return CustomerInterface
     * @throws InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     */
    protected function createCustomerSavePassword(CustomerInterface $customer, $password, $hash = 0)
    {
        if ($hash > 0) {
            $customer = $this->accountManagementInterface->createAccountWithPasswordHash($customer, $password);
        } else {
            $customer = $this->accountManagementInterface->createAccount($customer, $password);
        }
        return $customer;
    }


    /**
     * @param null $message
     * @param array $data
     * @return mixed
     */
    protected function getResponse($message = null, $data = [])
    {
        /** \Trans\Customer\Api\Data\CustomerIntegrationResponseInterface */
        $result = $this->response->create();

        $result->setDatas($data);
        $result->setMessage(__($message));

        return $result;
    }
}
