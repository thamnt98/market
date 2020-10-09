<?php

declare(strict_types=1);

namespace SM\Customer\Plugin\Magento\Customer\Api;

use Magento\Customer\Api\CustomerRepositoryInterface as BaseCustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\LocalizedException;
use SM\Customer\Helper\Config;
use SM\Customer\Model\Customer\Data\Checker as DataChecker;
use SM\Customer\Model\Email\Sender as EmailSender;

class CustomerRepositoryInterface
{
    /**
     * @var DataChecker
     */
    protected $dataChecker;

    /**
     * @var EmailSender
     */
    protected $emailSender;

    /**
     * CustomerRepositoryInterface constructor.
     * @param DataChecker $dataChecker
     * @param EmailSender $emailSender
     */
    public function __construct(
        DataChecker $dataChecker,
        EmailSender $emailSender
    ) {
        $this->dataChecker = $dataChecker;
        $this->emailSender = $emailSender;
    }

    /**
     * @param BaseCustomerRepositoryInterface $subject
     * @param \Closure $proceed
     * @param CustomerInterface $customerData
     * @param null|string $passwordHash
     * @return CustomerInterface
     * @throws LocalizedException
     */
    public function aroundSave(
        BaseCustomerRepositoryInterface $subject,
        \Closure $proceed,
        CustomerInterface $customerData,
        ?string $passwordHash = null
    ): CustomerInterface {
        if ($isRequireVerifiedEmail = $this->dataChecker->isRequireEmailVerified($customerData)) {
            $customerData->setCustomAttribute(Config::IS_VERIFIED_EMAIL_ATTRIBUTE_CODE, 0);
        }

        $savedCustomerData = $proceed($customerData, $passwordHash);

        if ($isRequireVerifiedEmail) {
            $this->emailSender->sendVerifyEmail($savedCustomerData);
            $this->emailSender->sendRegistrationSuccessEmail($savedCustomerData);
        }

        return $savedCustomerData;
    }
}
