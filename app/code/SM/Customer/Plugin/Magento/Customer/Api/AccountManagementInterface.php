<?php

declare(strict_types=1);

namespace SM\Customer\Plugin\Magento\Customer\Api;

use Closure;
use Magento\Customer\Api\AccountManagementInterface as BaseAccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\ForgotPasswordToken\GetCustomerByToken;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use SM\Customer\Api\TransCustomerRepositoryInterface;
use SM\Customer\Helper\Config;
use SM\Customer\Helper\Customer;
use SM\Customer\Model\Customer\Data\Checker as DataChecker;
use SM\Customer\Model\ValidateHash;
use SM\Customer\Model\Email\Sender as EmailSender;

class AccountManagementInterface
{
    /**
     * @var DataChecker
     */
    protected $dataChecker;

    /**
     * @var TransCustomerRepositoryInterface
     */
    protected $transCustomerRepository;
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;
    /**
     * @var Customer
     */
    protected $customerHelper;
    /**
     * @var GetCustomerByToken
     */
    protected $getByToken;

    /**
     * @var CustomerRegistry
     */
    protected $customerRegistry;

    /**
     * @var ValidateHash
     */
    protected $validateHash;

    protected $emailSender;

    /**
     * AccountManagementInterface constructor.
     * @param DataChecker $dataChecker
     * @param TransCustomerRepositoryInterface $transCustomerRepository
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param Customer $customerHelper
     * @param GetCustomerByToken $getByToken
     * @param CustomerRegistry $customerRegistry
     * @param ValidateHash $validateHash
     * @param EmailSender $emailSender
     */
    public function __construct(
        DataChecker $dataChecker,
        TransCustomerRepositoryInterface $transCustomerRepository,
        CustomerRepositoryInterface $customerRepositoryInterface,
        Customer $customerHelper,
        GetCustomerByToken $getByToken,
        CustomerRegistry $customerRegistry,
        ValidateHash $validateHash,
        EmailSender $emailSender
    ) {
        $this->validateHash = $validateHash;
        $this->customerRegistry = $customerRegistry;
        $this->getByToken = $getByToken;
        $this->customerHelper = $customerHelper;
        $this->dataChecker = $dataChecker;
        $this->transCustomerRepository = $transCustomerRepository;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->emailSender = $emailSender;
    }

    /**
     * @param BaseAccountManagementInterface $subject
     * @param string $username
     * @param string $password
     * @return array
     * @throws LocalizedException
     */
    public function beforeAuthenticate(BaseAccountManagementInterface $subject, string $username, string $password): array
    {
        if ($this->dataChecker->checkUsername($username) != Config::EMAIL_ATTRIBUTE_CODE) {
            $customer = $this->transCustomerRepository->getByPhone($username);
            if ($customer->getId()) {
                return [$customer->getEmail(), $password];
            }
        }
        return [$username, $password];
    }

    /**
     * @param BaseAccountManagementInterface $subject
     * @param string $email
     * @param string $resetToken
     * @param string $newPassword
     * @return array
     * @throws LocalizedException
     */
    public function beforeResetPassword(
        BaseAccountManagementInterface $subject,
        string $email,
        string $resetToken,
        string $newPassword
    ): array {
        if ($this->dataChecker->checkUsername($email) != Config::EMAIL_ATTRIBUTE_CODE) {
            // reset by phone
            $customerData = $this->transCustomerRepository->getByPhone($email);
            $email = $customerData->getEmail();
        }
        return [$email, $resetToken, $newPassword];
    }

    /**
     * @param BaseAccountManagementInterface $subject
     * @param Closure $proceed
     * @param string $email
     * @param string $template
     * @param int $websiteId
     * @return mixed
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundInitiatePasswordReset(
        BaseAccountManagementInterface $subject,
        Closure $proceed,
        $email,
        $template,
        $websiteId = null
    ) {
        try {
            $this->customerRepositoryInterface->get($email);
            return $proceed($email, $template, $websiteId);
        } catch (\Exception $e) {
            return 'You are not yet registered';
        }
    }

    /**
     * @param BaseAccountManagementInterface $subject
     * @param Closure $proceed
     * @param $customerId
     * @param $currentPassword
     * @param $newPassword
     * @return mixed
     * @throws InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Exception
     */
    public function aroundChangePasswordById(
        BaseAccountManagementInterface $subject,
        Closure $proceed,
        $customerId,
        $currentPassword,
        $newPassword
    ) {

        try {
            $this->validateHash->validate(
                $this->customerRegistry->retrieveSecureData($customerId),
                $newPassword
            );
            $result       = $proceed($customerId, $currentPassword, $newPassword);
            $customerData = $this->customerRepositoryInterface->getById($customerId);
            if ($result == true) {
                $this->customerHelper->logout($customerId);
                $this->emailSender->sendChangePassWord($customerData);
            }
            return $result;
        } catch (InputException $e) {
            throw $e;
        }
    }

    /**
     * @param BaseAccountManagementInterface $subject
     * @param Closure $proceed
     * @param string $email
     * @param string $resetToken
     * @param string $newPassword
     * @return mixed
     * @throws InputException
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\State\ExpiredException
     * @throws \Exception
     */
    public function aroundResetPassword(
        BaseAccountManagementInterface $subject,
        Closure $proceed,
        $email,
        $resetToken,
        $newPassword
    ) {
        if (!$email) {
            $customer = $this->getByToken->execute($resetToken);
            $email = $customer->getEmail();
        } else {
            $customer = $this->customerRepositoryInterface->get($email);
        }

        try {
            $this->validateHash->validate(
                $this->customerRegistry->retrieveSecureData($customer->getId()),
                $newPassword
            );
            return $proceed($email, $resetToken, $newPassword);
        } catch (InputException $e) {
            throw $e;
        }
    }
}
