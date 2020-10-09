<?php

declare(strict_types=1);

namespace SM\Customer\Model\Customer;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\SocialLogin\Model\ResourceModel\Social\CollectionFactory as SocialCollectionFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;

class Validator
{
    /**
     * @var TransCustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var SocialCollectionFactory
     */
    protected $socialCollectionFactory;

    /**
     * Validator constructor.
     * @param CustomerRepositoryInterface $customerRepository
     * @param SocialCollectionFactory $socialCollectionFactory
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        SocialCollectionFactory $socialCollectionFactory
    ) {
        $this->customerRepository = $customerRepository;
        $this->socialCollectionFactory = $socialCollectionFactory;
    }

    /**
     * @param string $email
     * @throws LocalizedException
     */
    public function validateEmail(string $email): void
    {
        if (!$email) {
            throw new LocalizedException(__('Could not get email'));
        }
    }

    /**
     * @param CustomerInterface $customerData
     * @param string $token
     * @throws LocalizedException
     */
    public function validateToken(CustomerInterface $customerData, string $token): void
    {
        if (!$token) {
            throw new LocalizedException(__('Could not get token'));
        }
        // Ignore weak encryption algorithm report due to customer 's requirement
        $buildInToken = md5($customerData->getId() . $customerData->getEmail()); //phpcs:ignore Magento2.Security.InsecureFunction.FoundWithAlternative

        if ($buildInToken != $token) {
            throw new LocalizedException(__('Token mismatch'));
        }
    }

    /**
     * @param $userName
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function verifySocialLogin($userName)
    {
        $customer = $this->customerRepository->get($userName);
        $customerId = $customer->getId();
        $socialCollectionFactory = $this->socialCollectionFactory->create();
        $customerSocial = $socialCollectionFactory->addFieldToFilter('customer_id', $customerId)->getFirstItem();

        if ($customerSocial->getId()) {
            return true;
        }

        return false;
    }

    public function getSocialProfileByEmail($email)
    {
        $customer = $this->customerRepository->get($email);
        $socialCollectionFactory = $this->socialCollectionFactory->create();
        $customerSocial = $socialCollectionFactory->addFieldToFilter('customer_id', $customer->getId())->getFirstItem();
        return $customerSocial;
    }
}
