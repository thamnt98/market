<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SM\Customer\Model\ResourceModel;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\ResourceModel\AddressRepository;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory as RegionCollectionFactory;
use Magento\Framework\App\Area;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\State;
use Magento\Framework\Encryption\EncryptorInterface as Encryptor;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\Rest\Request as RequestRestApi;
use SM\Customer\Helper\Config;
use SM\Customer\Model\Api\Data\ResultFactory;
use SM\Customer\Model\Customer\Validator as CustomerValidator;
use SM\Customer\Model\ResourceModel\Customer as ResourceModel;
use SM\GTM\Helper\Data;
use Trans\CustomerMyProfile\Helper\Data as TransCustomerHelper;
use Trans\LocationCoverage\Model\ResourceModel\City\CollectionFactory as CityCollection;

/**
 * Class CustomerRepository
 * @package SM\Customer\Model\ResourceModel
 */
class CustomerRepository implements \SM\Customer\Api\TransCustomerRepositoryInterface
{
    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var CustomerCollectionFactory
     */
    protected $customerCollectionFactory;

    /**
     * @var Config
     */
    protected $helper;

    /**
     * @var ResourceModel
     */
    protected $customerResourceModel;

    /**
     * @var Encryptor
     */
    protected $encryptor;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * @var \Trans\Customer\Api\AccountManagementInterface
     */
    protected $account;

    /**
     * @var CustomerInterface[]
     */
    protected $customerByPhone = [];

    /**
     * @var AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @var AddressInterfaceFactory
     */
    protected $addressFactory;

    /**
     * @var RegionCollectionFactory
     */
    protected $regionCollection;

    /**
     * @var CityCollection
     */
    protected $cityCollection;

    /**
     * @var AddressRepository
     */
    protected $addressRepository;
    /**
     * @var RequestRestApi
     */
    protected $requestRestApi;
    /**
     * @var State
     */
    protected $state;

    /**
     * @var CustomerValidator
     */
    protected $customerValidator;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;
    /**
     * @var \Zend\Log\Logger
     */
    protected $logger;

    /**
     * @var TransCustomerHelper
     */
    protected $transCustomerHelper;

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @var Data
     */
    protected $helperGtm;

    public function __construct(
        State $state,
        RequestRestApi $requestRestApi,
        Config $helper,
        Encryptor $encryptor,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Trans\Customer\Api\AccountManagementInterface $account,
        AccountManagementInterface $accountManagement,
        AddressInterfaceFactory $addressFactory,
        RegionCollectionFactory $regionCollection,
        CityCollection $cityCollection,
        AddressRepository $addressRepository,
        CustomerValidator $customerValidator,
        CustomerRepositoryInterface $customerRepositoryInterface,
        ResourceModel $customerResourceModel,
        CustomerCollectionFactory $customerCollectionFactory,
        TransCustomerHelper $transCustomerHelper,
        ResultFactory $resultFactory,
        Data $helperGtm
    ) {
        $this->state                       = $state;
        $this->requestRestApi              = $requestRestApi;
        $this->helper                      = $helper;
        $this->encryptor                   = $encryptor;
        $this->serializer                  = $serializer;
        $this->account                     = $account;
        $this->accountManagement           = $accountManagement;
        $this->addressFactory              = $addressFactory;
        $this->regionCollection            = $regionCollection;
        $this->cityCollection              = $cityCollection;
        $this->addressRepository           = $addressRepository;
        $this->customerValidator           = $customerValidator;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->customerResourceModel       = $customerResourceModel;
        $this->customerCollectionFactory   = $customerCollectionFactory;
        $this->transCustomerHelper         = $transCustomerHelper;
        $this->resultFactory               = $resultFactory;
        $this->helperGtm                   = $helperGtm;

        //Logger for debug
        $writer       = new \Zend\Log\Writer\Stream(BP . '/var/log/trans-api-request.log');
        $this->logger = new \Zend\Log\Logger();
        $this->logger->addWriter($writer);
    }

    /**
     * Retrieve customer by Telephone.
     *
     * @param string $telephone
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If customer with the specified telephone does not exist.
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByPhone($telephone)
    {
        $telephone = $this->helper->trimTelephone($telephone);

        if (empty($this->customerByPhone[$telephone])) {
            $id = $this->customerResourceModel->getCustomerIdByPhoneNumber($telephone);
            if (empty($id)) {
                throw new NoSuchEntityException(__('This mobile number is not registered'));
            }
            $this->customerByPhone[$telephone] = $this->customerRepositoryInterface->getById($id);
        }

        return $this->customerByPhone[$telephone];
    }

    /**
     * Create or Update customer
     * @param CustomerInterface $customer
     * @param null $passwordHash
     * @param null $password
     * @return CustomerInterface
     * @throws LocalizedException
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function transSave(CustomerInterface $customer, $passwordHash = null, $password = null)
    {
        $this->logRequest();
        try {
            $telephone = null;
            if ($customer->getCustomAttribute('telephone')) {
                $telephone = $customer->getCustomAttribute('telephone')->getValue();
            }
            //check telephone already exists with new account
            $entityExist = false;
            try {
                $entityExist = $this->getByPhone($telephone);
            } catch (NoSuchEntityException $e) {
                // This mean customer is not exist.
            }
            if ($customer->getId() == null && $entityExist) {
                throw new LocalizedException(
                    __(
                        'This %fieldName %fieldValue is already exists',
                        [
                            'fieldName'  => 'telephone',
                            'fieldValue' => $telephone
                        ]
                    )
                );
            }

            // Convert raw password to hashed password
            if ($password && empty($passwordHash)) {
                $passwordHash = $this->getPasswordHash($password);
            }

            if (!$customer->getId()) {
                $customer->setCustomAttribute('dob_change_number', 0);
            }

            //Edit customer
            if ($customer->getId()) {
                //verify dob change number
                $currentCustomerData = $this->customerRepositoryInterface->getById($customer->getId());
                $currentDobChangeNumber = $currentCustomerData->getCustomAttribute('dob_change_number');

                $currentDob = $currentCustomerData->getDob();
                $newDob = $customer->getDob();

                $currentDobChangeNumber = $currentDobChangeNumber == null ? 0 : (int)$currentDobChangeNumber->getValue();
                $dobChangeLimit = (int)$this->transCustomerHelper->getDobChangeLimit();
                if ($currentDob != $newDob) {
                    $nextDobChangeNumber = $currentDobChangeNumber + 1;
                    if ($currentDobChangeNumber <= $dobChangeLimit) {
                        $customer->setCustomAttribute('dob_change_number', (int)$currentDobChangeNumber + 1);
                    }

                    if ($nextDobChangeNumber == $dobChangeLimit || $currentDobChangeNumber >= $dobChangeLimit) {
                        $customer->setCustomAttribute('is_disabled_dob', 1);
                    }
                }
            }

            $city = $customer->getCustomAttribute('city')->getValue();
            $district = $customer->getCustomAttribute('district')->getValue();
            if ($city && $district) {
                if (!($storeGtmName = $customer->getCustomAttribute('store_name_gtm')) ||
                    !($storeGtmId = $customer->getCustomAttribute('store_id_gtm'))) {
                    if ($storeGtmData = $this->helperGtm->setCustomerStore($city, $district)) {
                        $customer->setCustomAttribute('store_name_gtm', $storeGtmData['store_name']);
                        $customer->setCustomAttribute('store_id_gtm', $storeGtmData['store_id']);
                    }
                }
            }

            //set flag to magento not validate quote, address.. after save so prevent error "Invalid state change requested"
            $customer->setData('ignore_validation_flag', true);
            $customerDataObject = $this->customerRepositoryInterface->save($customer, $passwordHash);

            //Add Incomplete address
            if (!$customer->getId()) {
                $this->addIncompleteAddress($customerDataObject);
            }
            $this->logResponse('Completed');

            return $customerDataObject;
        } catch (\Exception $e) {
            $this->logResponse($e->getMessage());
            throw new \Magento\Framework\Webapi\Exception(__($e->getMessage()));
        }
    }

    /**
     * @param $customer
     * @throws LocalizedException
     */
    public function addIncompleteAddress($customer)
    {
        $address = $this->addressFactory->create();
        $address->setCustomAttribute('address_tag', 'Home');
        $address->setTelephone($customer->getCustomAttribute('telephone')->getValue());
        $address->setRegionId($this->getRegionIdByCityCode($customer->getCustomAttribute('city')->getValue()));
        $address->setCity($customer->getCustomAttribute('city')->getValue());
        $address->setCustomAttribute('district', $customer->getCustomAttribute('district')->getValue());
        $address->setCustomerId($customer->getId());
        $address->setFirstname($customer->getFirstname());
        $address->setLastname($customer->getLastname());
        $address->setStreet(['N/A']);
        $address->setPostcode('*****');
        $address->setCountryId('ID');
        $address->setIsDefaultBilling(true);
        $address->setIsDefaultShipping(true);

        try {
            $this->addressRepository->save($address);
        } catch (LocalizedException $e) {
            throw new LocalizedException(__('Can not create incomplete address'));
        }
    }

    public function getRegionIdByCityCode($cityCode)
    {
        $regionId = null;
        $cityData = $this->cityCollection->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('entity_id', ['eq' => $cityCode])
            ->getItems();

        foreach ($cityData as $city) {
            $regionId = $city->getData('region_id');
        }

        $regionData = $this->regionCollection->create()
            ->addFieldToFilter('country_id', 'ID')
            ->addFieldToFilter('code', ['eq' => $regionId])
            ->getData();

        foreach ($regionData as $region) {
            $regionId = (int)$region['region_id'];
        }

        return $regionId;
    }

    /**
     * @param string $email
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function uniqueEmail($email)
    {
        if (!$this->isEmail($email)) {
            throw new LocalizedException(__('Invalid email!'));
        }

        try {
            $this->customerRepositoryInterface->get($email);
            return false;
        } catch (NoSuchEntityException $e) {
            $this->logResponse($e->getMessage());
        } catch (\Exception $e) {
            $this->logResponse($e->getMessage());
        }

        /**
         * Check CDB
         */
        $checkCentral = $this->account->checkCustomerRegister('', $email);
        try {
            $checkCentral = $this->serializer->unserialize($checkCentral);
            if (isset($checkCentral['customer_email']) && $checkCentral['customer_email'] == 1) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * @param string $email
     * @return \SM\Customer\Api\Data\ResultInterface | bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function verifyEmail($email)
    {
        $uniqueEmail        = $this->uniqueEmail($email);
        $customerCollection = $this->customerCollectionFactory->create();
        $customer           = $customerCollection->addFieldToFilter('email', $email)->getItems();

        if (!$uniqueEmail && !empty($customer)) {
            $validateSocialLogin = $this->customerValidator->verifySocialLogin($email);
            $customerSocial      = $this->customerValidator->getSocialProfileByEmail($email);

            if ($validateSocialLogin) {
                $result = $this->resultFactory->create();
                $result->setMessage(__('You are linked with %1. Please sign in with your %1 account'));
                $result->setArgument($customerSocial->getType());
                return $result;
            }
        }

        return $uniqueEmail;
    }

    /**
     * @param string $telephone
     * @return bool
     * @throws LocalizedException
     */
    public function uniquePhone($telephone)
    {
        if (!$this->isPhone($telephone)) {
            throw new LocalizedException(__('Invalid telephone number!'));
        }
        $telephoneIncludePrefix = $telephone;
        try {
            $telephone = $this->helper->trimTelephone($telephone);
            $id        = $this->customerResourceModel->getCustomerIdByPhoneNumber($telephone);
            if ($id) {
                return false;
            }
        } catch (NoSuchEntityException $e) {
            $this->logResponse($e->getMessage());
        } catch (\Exception $e) {
            $this->logResponse($e->getMessage());
        }

        /**
         * Check CDB
         */
        $checkCentral = $this->account->checkCustomerRegister($telephoneIncludePrefix);
        try {
            $checkCentral = $this->serializer->unserialize($checkCentral);
            if (isset($checkCentral['customer_phone']) && $checkCentral['customer_phone'] == 1) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * @param null $exception
     * @throws LocalizedException
     */
    protected function logResponse($exception = null)
    {
        if ($this->state->getAreaCode() == Area::AREA_WEBAPI_REST && $exception != null) {
            $this->logger->info("Response: " . $exception, []);
        }
    }

    /**
     * @throws LocalizedException
     */
    protected function logRequest()
    {
        if ($this->state->getAreaCode() == Area::AREA_WEBAPI_REST) {
            $bodyParams = $this->requestRestApi->getBodyParams();

            $this->logger->info("Request to " . $this->requestRestApi->getRequestUri(), $bodyParams);
        }
    }

    /**
     * Check is email
     * @param string $mail
     * @return bool
     */
    protected function isEmail($mail)
    {
        return (!preg_match("/[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+.[a-zA-Z]{2,4}/", $mail)) ? false : true;
    }

    protected function isPhone($phone)
    {
        return (!preg_match("/^\+?(62|08|8)[0-9]+$/", $phone)) ? false : true;
    }

    public function getPasswordHash($password)
    {
        return $this->encryptor->getHash($password, true);
    }
}
