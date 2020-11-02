<?php
/**
 * @category    SM
 * @package     SM_Customer
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Nam Nguyen <namnd2@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace Trans\Customer\Controller\Address;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use SM\CustomPrice\Helper\Customer;

/**
 * Class SetDefault
 * @package Trans\Customer\Controller\Address
 */
class SetDefault extends \Magento\Framework\App\Action\Action implements HttpPostActionInterface
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
     * @var Customer
     */
    protected $customerHelper;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * SetDefault constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\AddressFactory $addressFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        Customer $customerHelper,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        $this->customerSession = $customerSession;
        $this->customerSession = $customerSession;
        $this->customerHelper = $customerHelper;
        $this->addressFactory = $addressFactory;
        $this->customerRepository = $customerRepository;
        parent::__construct($context);
    }

    /**
     * Set default billing shipping address
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (!$this->getRequest()->getParam('id')) {
            $this->messageManager->addErrorMessage(__('Address Id is required.'));
            return $this->_redirect('customer/address');
        }
        if (!$this->customerSession->isLoggedIn()) {
            $this->messageManager->addErrorMessage(__('Please login!'));
            return $this->_redirect('customer/address');
        }
        $address = $this->addressFactory->create()->load($this->getRequest()->getParam('id'));
        if ($address->getId() && $address->getCustomerId() == $this->customerSession->getCustomerId()) {
            $address->setIsDefaultShipping('1')->setIsDefaultBilling('1');
            $latitude = $address->getLatitude();
            $longitude = $address->getLongitude();
            try {
                $address->save();
                $this->messageManager->addSuccessMessage(__('You have changed your main address.'));
                $customer = $this->customerSession->getCustomerDataObject();
                if ($customer->getCustomAttribute('is_edit_address')) {
                    if ($latitude == '' || $longitude == '') {
                        $customer->getCustomAttribute('is_edit_address')->setValue(0);
                        $customer->setData('ignore_validation_flag', true);
                    } else {
                        $customer->getCustomAttribute('is_edit_address')->setValue(1);
                        $customer->setData('ignore_validation_flag', true);
                    }
                    $this->customerRepository->save($customer);
                }

                $customer = $this->customerSession->getCustomer();
                $this->customerHelper->updateDistrictAndOmniStoreForCustomer($customer, $address->getDistrict(), $address->getCity());
//                $this->messageManager->addSuccessMessage(__('You saved the address.'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        } else {
            $this->messageManager->addErrorMessage(__('You can not save this address.'));
        }
        return $this->_redirect('customer/address');
    }
}
