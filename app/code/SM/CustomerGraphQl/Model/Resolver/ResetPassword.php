<?php

namespace SM\CustomerGraphQl\Model\Resolver;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthenticationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use SM\AndromedaSms\Model\SmsVerification\Validator;
use SM\Customer\Helper\Config;
use SM\Customer\Helper\Customer as CustomerHelper;
use Magento\Customer\Model\CustomerRegistry;
use SM\Customer\Model\ValidateHash;
use Magento\Framework\Encryption\EncryptorInterface;

/**
 * Class ResetPassword
 * @package SM\CustomerGraphQl\Model\Resolver
 */
class ResetPassword implements ResolverInterface
{
    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var Config
     */
    protected $customerConfigHelper;

    /**
     * @var CustomerHelper
     */
    protected $customerHelper;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;
    /**
     * @var CustomerRegistry
     */
    protected $customerRegistry;
    /**
     * @var ValidateHash
     */
    protected $validateHash;
    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * ResetPassword constructor.
     * @param Validator $validator
     * @param Config $customerConfigHelper
     * @param CustomerHelper $customerHelper
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerRegistry $customerRegistry
     * @param ValidateHash $validateHash
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        Validator $validator,
        Config $customerConfigHelper,
        CustomerHelper $customerHelper,
        CustomerRepositoryInterface $customerRepository,
        CustomerRegistry $customerRegistry,
        ValidateHash $validateHash,
        EncryptorInterface $encryptor
    )
    {
        $this->validator = $validator;
        $this->customerConfigHelper = $customerConfigHelper;
        $this->customerHelper = $customerHelper;
        $this->customerRepository = $customerRepository;
        $this->customerRegistry = $customerRegistry;
        $this->validateHash = $validateHash;
        $this->encryptor = $encryptor;
    }

    /**
     * @param Field $field
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     * @throws GraphQlAuthenticationException
     * @throws GraphQlInputException
     * @throws LocalizedException
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (empty($args['telephone']) || !$args['telephone']) {
            throw new GraphQlInputException(__('"telephone" value should be specified'));
        }
        $telephone = $args['telephone'];

        if (empty($args['new_password']) || !$args['new_password']) {
            throw new GraphQlInputException(__('"new password" value should be specified'));
        }

        $newPassword = $args['new_password'];

        // Check telephone exists

        try {
            $convertPhoneNumber = $this->customerConfigHelper->trimTelephone($telephone);
            $customerId = (int)$this->customerHelper->getByPhone($convertPhoneNumber);
            $customer = $this->customerRepository->getById($customerId);
        } catch (NoSuchEntityException $e) {
            throw new GraphQlInputException(__('The phone number `%s` does not exits', $telephone));
        }

        // Checking customer has been locked
        $isCustomerLocked = $this->customerHelper->isCustomerLock($customerId);
        $isRequestTokenLocked = $this->customerHelper->isRequestTokenLock($customer);

        if ($isCustomerLocked || $isRequestTokenLocked) {
            throw new GraphQlAuthenticationException(__('Your account is locked!'));
        }

        try {
            // Check telephone is verified
            $this->validator->validateVerified($telephone);
        } catch (LocalizedException $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }

        // Change password
        try {
            $customerSecure = $this->customerRegistry->retrieveSecureData($customerId);
            $this->validateHash->validate($customerSecure, $newPassword);

            $customerSecure->setRpToken(null);
            $customerSecure->setRpTokenCreatedAt(null);
            $customerSecure->setPasswordHash($this->encryptor->getHash($newPassword, true));

            $this->customerRepository->save($customer);

            // Logout
            $this->customerHelper->logout($customerId);
        } catch (\Exception $e) {
            return [
                'status' => 0,
                'message' => $e->getMessage()
            ];
        }
        return [
            'status' => 1,
            'message' => "You reset password successfully"
        ];
    }
}
