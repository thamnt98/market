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

        $customerChecker = $this->dataChecker->createCustomerChecker($customerData);

        $savedCustomerData = $proceed($customerData, $passwordHash);

        if ($savedCustomerData->getCustomAttribute(Config::IS_VERIFIED_EMAIL_ATTRIBUTE_CODE)->getValue() == 0) {
            if ($customerChecker[DataChecker::IS_CHANGE_EMAIL]) {
                $this->emailSender->sendChangeEmail($savedCustomerData);
            } else {
                $this->emailSender->sendVerifyEmail($savedCustomerData);
            }
        }

        if ($customerChecker[DataChecker::IS_CHANGE_EMAIL]) {
            $this->emailSender->sendChangeEmail($savedCustomerData);
        }

        if ($customerChecker[DataChecker::IS_CHANGE_TELEPHONE]) {
            $this->emailSender->sendChangeTelephoneEmail($savedCustomerData);
        }

        if ($customerChecker[DataChecker::IS_CHANGE_PERSONAL_INFO]) {
            $this->emailSender->sendChangePersonalInformation($savedCustomerData);
        }

        return $savedCustomerData;
    }
}
