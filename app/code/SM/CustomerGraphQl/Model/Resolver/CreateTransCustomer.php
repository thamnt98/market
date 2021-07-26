<?php
namespace SM\CustomerGraphQl\Model\Resolver;

use SM\AndromedaSms\Model\SmsVerification\Validator;
use Magento\Customer\Api\CustomerRepositoryInterface;
use SM\Customer\Api\TransCustomerRepositoryInterface;
use Trans\Customer\Api\AccountManagementInterface as TransAccount;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\CustomerGraphQl\Model\Customer\ValidateCustomerData;
use Magento\CustomerGraphQl\Model\Customer\ExtractCustomerData;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class CreateTransCustomer
 * @package SM\CustomerGraphQl\Model\Resolver
 */
class CreateTransCustomer implements ResolverInterface
{
    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var TransCustomerRepositoryInterface
     */
    protected $transCustomerRepository;

    /**
     * @var TransAccount
     */
    protected $transAccount;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var CustomerInterfaceFactory
     */
    protected $customerFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var ValidateCustomerData
     */
    protected $validateCustomerData;

    /**
     * @var ExtractCustomerData
     */
    protected $extractCustomerData;

    /**
     * CreateTransCustomer constructor.
     * @param Validator $validator
     * @param CustomerRepositoryInterface $customerRepository
     * @param TransCustomerRepositoryInterface $transCustomerRepository
     * @param TransAccount $transAccount
     * @param SerializerInterface $serializer
     * @param AccountManagementInterface $accountManagement
     * @param DataObjectProcessor $dataObjectProcessor
     * @param CustomerInterfaceFactory $customerFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param ValidateCustomerData $validateCustomerData
     * @param ExtractCustomerData $extractCustomerData
     */
    public function __construct(
        Validator $validator,
        CustomerRepositoryInterface $customerRepository,
        TransCustomerRepositoryInterface $transCustomerRepository,
        TransAccount $transAccount,
        SerializerInterface $serializer,
        AccountManagementInterface $accountManagement,
        DataObjectProcessor $dataObjectProcessor,
        CustomerInterfaceFactory $customerFactory,
        DataObjectHelper $dataObjectHelper,
        ValidateCustomerData $validateCustomerData,
        ExtractCustomerData $extractCustomerData
    ) {
        $this->validator = $validator;
        $this->customerRepository = $customerRepository;
        $this->transCustomerRepository = $transCustomerRepository;
        $this->transAccount = $transAccount;
        $this->serializer = $serializer;
        $this->accountManagement = $accountManagement;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->customerFactory = $customerFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->validateCustomerData = $validateCustomerData;
        $this->extractCustomerData = $extractCustomerData;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (empty($args['input']) || !is_array($args['input'])) {
            throw new GraphQlInputException(__('"input" value should be specified'));
        }

        if (empty($args['input']['telephone']) || !($telephone = $args['input']['telephone'])) {
            throw new GraphQlInputException(__('"telephone" value should be specified'));
        }

        try {
            $this->validator->validateVerified($telephone);
        } catch (LocalizedException $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }

        if (strlen($telephone) < 10 || strlen($telephone) > 16) {
            throw new GraphQlInputException(__("Make sure phone number follow the format"));
        }

        /**
         * Check telephone already exists
         */
        try {
            $customerExist = $this->transCustomerRepository->getByPhone($telephone);
            if (!empty($customerExist->getId())) {
                throw new GraphQlInputException(__('Mobile number has already been registered.'));
            }
        } catch (NoSuchEntityException $e) {
            /**
             * Continue Create account
             */
        }

        /**
         * Check email already exists
         */
        if ($email = $args['input']['email']) {
            try {
                $customerExist = $this->customerRepository->get($email);
                if (!empty($customerExist->getId())) {
                    throw new GraphQlInputException(__('Email has already been registered.'));
                }
            } catch (NoSuchEntityException $e) {
                /**
                 * Continue Create account
                 */
            }
        } else {
            throw new GraphQlInputException(__('Email field is required'));
        }

        /**
         * Check CDB
         */
        $checkCentral = $this->transAccount->checkCustomerRegister($telephone, $email);
        try {
            $checkCentral = $this->serializer->unserialize($checkCentral);
            if (isset($checkCentral['customer_email']) && $checkCentral['customer_email'] == 1) {
                throw new GraphQlInputException(__('Email has already been registered.'));
            }
            if (isset($checkCentral['customer_phone']) && $checkCentral['customer_phone'] == 1) {
                throw new GraphQlInputException(__('Mobile number has already been registered'));
            }
        } catch (\Exception $e) {
            throw new GraphQlInputException(__('We can\'t save the customer.'));
        }

        if (isset($args['input']['date_of_birth'])) {
            $args['input']['dob'] = $args['input']['date_of_birth'];
        }

        try {
            $customer = $this->createAccount($args['input'], $context->getExtensionAttributes()->getStore());
        } catch (LocalizedException $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }

        $data = $this->extractCustomerData->execute($customer);
        return ['customer' => $data];
    }

    /**
     * Create account
     *
     * @param array $data
     * @param StoreInterface $store
     * @return CustomerInterface
     * @throws LocalizedException
     */
    private function createAccount(array $data, StoreInterface $store): CustomerInterface
    {
        $customerDataObject = $this->customerFactory->create();
        /**
         * Add required attributes for customer entity
         */
        $requiredDataAttributes = $this->dataObjectProcessor->buildOutputDataArray(
            $customerDataObject,
            CustomerInterface::class
        );
        $data = array_merge($requiredDataAttributes, $data);
        $this->validateCustomerData->execute($data);
        $this->dataObjectHelper->populateWithArray(
            $customerDataObject,
            $data,
            CustomerInterface::class
        );
        $customerDataObject->setWebsiteId($store->getWebsiteId());
        $customerDataObject->setStoreId($store->getId());

        $password = array_key_exists('password', $data) ? $data['password'] : null;
        return $this->accountManagement->createAccount($customerDataObject, $password);
    }
}
