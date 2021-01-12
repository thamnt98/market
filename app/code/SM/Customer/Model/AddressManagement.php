<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Customer
 *
 * Date: May, 06 2020
 * Time: 11:30 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Customer\Model;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Setup\Exception;
use SM\CustomPrice\Helper\Customer;

class AddressManagement implements \SM\Customer\Api\AddressManagementInterface
{

    /**
     * @var \Trans\Customer\Helper\Config
     */
    protected $transCustomerConfig;

    /**
     * @var \Magento\Customer\Model\ResourceModel\AddressRepository
     */
    protected $addressRepository;

    /**
     * @var \Magento\Customer\Api\AddressMetadataInterface
     */
    protected $addressMetadata;
    /**
     * @var \Magento\Customer\Model\Metadata\ElementFactory
     */
    protected $elementFactory;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;
    /**
     * @var Customer
     */
    protected $customerHelper;

    /**
     * AddressManagement constructor.
     *
     * @param \Magento\Customer\Model\CustomerFactory                 $customerFactory
     * @param \Trans\Customer\Helper\Config                           $transCustomerConfig
     * @param \Magento\Customer\Model\ResourceModel\AddressRepository $addressRepository
     * @param \Magento\Customer\Api\AddressMetadataInterface          $addressMetadata
     * @param \Magento\Customer\Model\Metadata\ElementFactory         $elementFactory
     * @param Customer                                                $customerHelper
     * @param \Magento\Customer\Api\CustomerRepositoryInterface       $customerRepository
     */
    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Trans\Customer\Helper\Config $transCustomerConfig,
        \Magento\Customer\Model\ResourceModel\AddressRepository $addressRepository,
        \Magento\Customer\Api\AddressMetadataInterface $addressMetadata,
        \Magento\Customer\Model\Metadata\ElementFactory $elementFactory,
        Customer $customerHelper,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        $this->transCustomerConfig = $transCustomerConfig;
        $this->addressRepository = $addressRepository;
        $this->addressMetadata = $addressMetadata;
        $this->elementFactory = $elementFactory;
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->customerHelper = $customerHelper;
    }

    /**
     * @param $customerId
     * @param \Magento\Customer\Api\Data\AddressInterface $addressData
     * @return \Magento\Customer\Api\Data\AddressInterface
     * @throws LocalizedException
     */
    public function save($customerId, $addressData)
    {
        if (is_null($addressData->getTelephone()) ||
            strlen($addressData->getTelephone()) < 10 ||
            strlen($addressData->getTelephone()) > 16) {
            throw new InputException(__("Make sure you follow the format"));
        }

        if (is_null($addressData->getPostcode()) ||
            !is_numeric($addressData->getPostcode()) ||
            strlen($addressData->getPostcode()) != 5) {
            throw new InputException(__("Zipcode should be 5 digits only"));
        }

        try {
            $addressData->setCustomerId($customerId);
            $addressModel=$this->addressRepository->save($addressData);
            $customer = $this->customerFactory->create()->load($customerId);
            if ($addressData->isDefaultBilling()||$addressData->isDefaultShipping()) {
                $customerData  = $customer->getDataModel();
                $isEditAddress = $customerData->getCustomAttribute('is_edit_address')->getValue();
                if ($isEditAddress == 0) {
                    $customer->setData('ignore_validation_flag', true);
                    $customer->setData('is_edit_address', 1);
                    $customer->save();
                }
                $this->customerHelper->updateDistrictAndOmniStoreForCustomer($customer, $addressData->getCustomAttribute('district')->getValue(), $addressData->getCity());
            }
            return $addressModel;
        } catch (LocalizedException $e) {
            throw new LocalizedException(__('We can\'t save the address right now.'));
        }
    }

    /**
     * @param int $customerId
     * @param int $addressId
     * @return bool
     * @throws Exception
     * @throws NoSuchEntityException
     */
    public function delete($customerId, $addressId)
    {
        try {
            $address = $this->addressRepository->getById($addressId);
            if ($address->getCustomerId() == $customerId) {
                $this->addressRepository->deleteById($addressId);
            } else {
                return false;
            }
        } catch (NoSuchEntityException $e) {
            throw new NoSuchEntityException(__($e->getMessage()));
        } catch (LocalizedException $e) {
            throw new Exception(__('Sorry , We can\'t delete the address right now.'));
        }

        return true;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function validateMaxAddress($customerId)
    {
        try {
            /** @var \Magento\Customer\Model\Customer $customer */
            $customer = $this->customerFactory->create();
            $customer = $customer->load($customerId);
            $addressList = $customer->getAddressesCollection();
        } catch (\Exception $e) {
            throw new LocalizedException(__('We can\'t create new address right now. Please try again later!'));
        }

        $limit = $this->transCustomerConfig->getConfigValue('sm_customer/customer_address_limit/limit');
        if ($addressList->getSize() >= (int)$limit) {
            throw new LocalizedException(
                __(
                    "You can only save up to %1 addresses. To add a new one, please delete the existing address.",
                    $limit
                )
            );
        }
        return true;
    }
}
