<?php

namespace SM\Checkout\Model;

/**
 * @api
 */
class AccountManagement implements \SM\Checkout\Api\AccountManagementInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Customer\Model\AddressFactory
     */
    protected $addressFactory;

    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    protected $addressRepository;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * @var \Magento\Customer\Model\Address\CustomerAddressDataFormatter
     */
    protected $customerAddressDataFormatter;
    /**
     * @var \SM\Checkout\Api\Data\AccountManagementResponseInterfaceFactory
     */
    protected $accountManagementResponse;

    /**
     * AccountManagement constructor.
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\AddressFactory $addressFactory
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param \Magento\Customer\Model\Address\CustomerAddressDataFormatter $customerAddressDataFormatter
     * @param \SM\Checkout\Api\Data\AccountManagementResponseInterfaceFactory $accountManagementResponse
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\Customer\Model\Address\CustomerAddressDataFormatter $customerAddressDataFormatter,
        \SM\Checkout\Api\Data\AccountManagementResponseInterfaceFactory $accountManagementResponse
    ) {
        $this->customerSession = $customerSession;
        $this->addressFactory = $addressFactory;
        $this->addressRepository = $addressRepository;
        $this->serializer = $serializer;
        $this->customerAddressDataFormatter = $customerAddressDataFormatter;
        $this->accountManagementResponse = $accountManagementResponse;
    }

    /**
     * {@inheritdoc}
     */
    public function save($address)
    {
        $status = false;
        $result = ['message' => __('Please login before!')];
        if ($this->customerSession->isLoggedIn()) {
            unset($address['city_text']);
            if (isset($address['fullname'])) {
                $fullName = $address['fullname'];
                $fullNameArray = explode(" ", $fullName);
                if (count($fullNameArray) == 1) {
                    $address['firstname'] = $fullName;
                    $address['lastname'] = $fullName;
                } else {
                    foreach ($fullNameArray as $key => $text) {
                        if ($key == 0) {
                            $address['firstname'] = $text;
                        } else {
                            if (isset($address['lastname'])) {
                                $address['lastname'] .= ' ' . $text;
                            } else {
                                $address['lastname'] = $text;
                            }
                        }
                    }
                }
                unset($address['fullname']);
            }
            $address['parent_id'] = $this->customerSession->getId();
            $addressModel = $this->addressFactory->create();
            $addressModel->setCustomerId($this->customerSession->getId());
            $addressModel->setData($address);
            $addressModel->setIsDefaultBilling('0');
            $addressModel->setIsDefaultShipping('0');
            $addressModel->setSaveInAddressBook('1');
            try {
                $addressModel->save();
                $status = true;
                //$result = $addressModel->getData();
                $address = $this->addressRepository->getById($addressModel->getId());
                $result = $this->customerAddressDataFormatter->prepareAddress($address);
            } catch (\Exception $e) {
                $result = ['message' => $e->getMessage()];
            }
        }
        return $this->accountManagementResponse->create()->setStatus($status)->setResult($this->serializer->serialize($result));
    }
}
