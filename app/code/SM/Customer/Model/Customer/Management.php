<?php

declare(strict_types=1);

namespace SM\Customer\Model\Customer;

use Magento\Customer\Model\AccountManagement;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random;
use SM\Customer\Api\TransCustomerRepositoryInterface;

class Management
{
    /**
     * @var TransCustomerRepositoryInterface
     */
    protected $transCustomerRepository;

    /**
     * @var AccountManagement
     */
    protected $accountManagement;

    /**
     * @var Random
     */
    protected $mathRandom;

    /**
     * ActionResolver constructor.
     * @param TransCustomerRepositoryInterface $transCustomerRepository
     * @param AccountManagement $accountManagement
     * @param Random $mathRandom
     */
    public function __construct(
        TransCustomerRepositoryInterface $transCustomerRepository,
        AccountManagement $accountManagement,
        Random $mathRandom
    ) {
        $this->transCustomerRepository = $transCustomerRepository;
        $this->accountManagement = $accountManagement;
        $this->mathRandom = $mathRandom;
    }

    /**
     * @param string $phoneNumber
     * @return string
     * @throws LocalizedException
     */
    public function updateResetPasswordToken(string $phoneNumber): string
    {
        $customer = $this->transCustomerRepository->getByPhone($phoneNumber);
        $newPasswordToken = $this->mathRandom->getUniqueHash();
        $this->accountManagement->changeResetPasswordLinkToken($customer, $newPasswordToken);
        return $newPasswordToken;
    }
}
