<?php

declare(strict_types=1);

namespace SM\Checkout\Plugin\Quote\Api;

class CartManagement
{
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    protected $addressRepository;

    /**
     * CartManagement constructor.
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepository
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
    ) {
        $this->cartRepository = $cartRepository;
        $this->addressRepository = $addressRepository;
    }

    /**
     * @param \Magento\Quote\Api\CartManagementInterface $subject
     * @param $customerId
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function beforeGetCartForCustomer(
        \Magento\Quote\Api\CartManagementInterface $subject,
        $customerId
    ) {
        try {
            $quote = $this->cartRepository->getActiveForCustomer($customerId);
            if ($quote->isMultipleShippingAddresses()) {
                foreach ($quote->getAllShippingAddresses() as $address) {
                    $quote->removeAddress($address->getId());
                }

                $shippingAddress = $quote->getShippingAddress();
                $defaultShipping = $quote->getCustomer()->getDefaultShipping();
                if ($defaultShipping) {
                    $defaultCustomerAddress = $this->addressRepository->getById($defaultShipping);
                    $shippingAddress->importCustomerAddressData($defaultCustomerAddress);
                }
                $this->cartRepository->save($quote);
            }
        } catch (\Exception $e) {
        }
    }
}
