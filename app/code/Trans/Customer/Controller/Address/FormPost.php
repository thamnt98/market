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

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\RegionInterfaceFactory;
use Magento\Customer\Model\Metadata\FormFactory;
use Magento\Customer\Model\Session;
use Magento\Directory\Helper\Data as HelperData;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Class FormPost
 * @package Trans\Customer\Controller\Address
 */
class FormPost extends \Magento\Customer\Controller\Address\FormPost
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * FormPost constructor.
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param Context $context
     * @param Session $customerSession
     * @param FormKeyValidator $formKeyValidator
     * @param FormFactory $formFactory
     * @param AddressRepositoryInterface $addressRepository
     * @param AddressInterfaceFactory $addressDataFactory
     * @param RegionInterfaceFactory $regionDataFactory
     * @param DataObjectProcessor $dataProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param ForwardFactory $resultForwardFactory
     * @param PageFactory $resultPageFactory
     * @param RegionFactory $regionFactory
     * @param HelperData $helperData
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        CustomerRepositoryInterface $customerRepository,
        Context $context,
        Session $customerSession,
        FormKeyValidator $formKeyValidator,
        FormFactory $formFactory,
        AddressRepositoryInterface $addressRepository,
        AddressInterfaceFactory $addressDataFactory,
        RegionInterfaceFactory $regionDataFactory,
        DataObjectProcessor $dataProcessor,
        DataObjectHelper $dataObjectHelper,
        ForwardFactory $resultForwardFactory,
        PageFactory $resultPageFactory,
        RegionFactory $regionFactory,
        HelperData $helperData
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->customerRepository = $customerRepository;
        parent::__construct(
            $context,
            $customerSession,
            $formKeyValidator,
            $formFactory,
            $addressRepository,
            $addressDataFactory,
            $regionDataFactory,
            $dataProcessor,
            $dataObjectHelper,
            $resultForwardFactory,
            $resultPageFactory,
            $regionFactory,
            $helperData
        );
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $redirectUrl = null;
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        if (!$this->getRequest()->isPost()) {
            $this->_getSession()->setAddressFormData($this->getRequest()->getPostValue());
            return $this->resultRedirectFactory->create()->setUrl(
                $this->_redirect->error($this->_buildUrl('*/*/edit'))
            );
        }

        if (!$this->getRequest()->getParam('id') && $this->_getSession()->isLoggedIn()) {
            $limit = $this->_objectManager->get(\Trans\Customer\Helper\Config::class)->getConfigValue('sm_customer/customer_address_limit/limit');
            $addressList = $this->_getSession()->getCustomer()->getAddressesCollection();
            if ($addressList->getSize() >= (int)$limit) {
                $this->messageManager->addErrorMessage(__('We can\'t save the address.'));
                return $this->resultRedirectFactory->create()->setUrl(
                    $this->_redirect->error($this->_buildUrl('*/*/index'))
                );
            }
        }

        try {
            /** @var \Magento\Customer\Model\Data\Address $address */
            $address = $this->_extractAddress();
            $id      = $address->getId();

            if ($address->getCustomAttribute('latitude')->getValue() == '') {
                $address->getCustomAttribute('latitude')->setValue(-6.175392);
            }
            if ($address->getCustomAttribute('longitude')->getValue() == '') {
                $address->getCustomAttribute('longitude')->setValue(106.827153);
            }

            $customer = $this->_customerSession->getCustomerDataObject();
            if (
                $customer->getCustomAttribute('is_edit_address')
                && ($address->isDefaultBilling() == 1 || $address->isDefaultShipping() == 1)
            ) {
                $customer->getCustomAttribute('is_edit_address')->setValue(1);
                $customer->setData('ignore_validation_flag', true);
                $this->customerRepository->save($customer);
            }

            $this->_addressRepository->save($address);

            if ($id) {
                $this->messageManager->addSuccessMessage(__('You have edited your address.'));
            } else {
                $this->messageManager->addSuccessMessage(__('You have successfully added a new address.'));
            }
            if ($this->getRequest()->getParam('redirect_checkout') && $this->getRequest()->getParam('redirect_checkout') == 'true') {
                $currentAddressId = $currentAction = false;
                if ($this->getRequest()->getParam('current_address_id') && $this->getRequest()->getParam('current_address_id') != '') {
                    $currentAddressId = $this->getRequest()->getParam('current_address_id');
                }
                if ($this->getRequest()->getParam('action_after') && $this->getRequest()->getParam('action_after') != '') {
                    $currentAction = $this->getRequest()->getParam('action_after');
                }
                if ($currentAddressId && $currentAction) {
                    $this->checkoutSession->setCurrentAddressId($currentAddressId);
                    $this->checkoutSession->setCurrentAction($currentAction);
                }
                $url = $this->_buildUrl('transcheckout/index/index', ['_secure' => true]);
            } else {
                $url = $this->_buildUrl('*/*/index', ['_secure' => true]);
            }
            return $this->resultRedirectFactory->create()->setUrl($this->_redirect->success($url));
        } catch (InputException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            foreach ($e->getErrors() as $error) {
                $this->messageManager->addErrorMessage($error->getMessage());
            }
        } catch (\Exception $e) {
            $redirectUrl = $this->_buildUrl('*/*/index');
            $this->messageManager->addExceptionMessage($e, __('We can\'t save the address.'));
        }

        $url = $redirectUrl;
        if (!$redirectUrl) {
            $this->_getSession()->setAddressFormData($this->getRequest()->getPostValue());
            $url = $this->_buildUrl('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }

        return $this->resultRedirectFactory->create()->setUrl($this->_redirect->error($url));
    }
}
