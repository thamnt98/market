<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  Mageplaza
 * @package   Mageplaza_SocialLogin
 * @copyright Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license   https://www.mageplaza.com/LICENSE.txt
 */

namespace SM\Customer\Controller\Social;

use Exception;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\SocialLogin\Controller\Social\Login as BaseLoginController;
use Mageplaza\SocialLogin\Helper\Social as SocialHelper;
use Mageplaza\SocialLogin\Model\Social;
use SM\Customer\Model\Social as SMSocialModel;
use SM\Customer\Model\ResourceModel\CustomerRepository as SMCustomerResourceReposi;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Class Login
 * @package SM\Customer\Controller\Social
 */
class Register extends BaseLoginController
{
    /**
     * @var SMCustomerResourceReposi
     */
    protected $smCustomerResourceRepo;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    public function __construct(
        SMSocialModel $socialModel,
        Context $context,
        StoreManagerInterface $storeManager,
        AccountManagementInterface $accountManager,
        SocialHelper $apiHelper,
        Social $apiObject,
        Session $customerSession,
        AccountRedirect $accountRedirect,
        RawFactory $resultRawFactory,
        SMCustomerResourceReposi $smCustomerResourceRepo,
        CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($context, $storeManager, $accountManager, $apiHelper, $apiObject, $customerSession,
            $accountRedirect, $resultRawFactory);
        $this->apiObject = $socialModel;
        $this->smCustomerResourceRepo = $smCustomerResourceRepo;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @return ResponseInterface|Raw|ResultInterface|Login|void
     * @throws FailureToSendException
     * @throws InputException
     * @throws LocalizedException
     */
    public function execute()
    {
        $userProfile = $this->session->getUserProfile();
        $type = $this->session->getSocialType();
        $userProfile->phone = $this->getRequest()->getParam('telephone');
        $userProfile->city = $this->getRequest()->getParam('city');
        $userProfile->district = $this->getRequest()->getParam('district');
        $customer = $this->createCustomerProcess($userProfile, $type);
        $this->session->setSocialType(NULL)
            ->setActionType(NULL)
            ->setUserProfile(NULL);

        //create address default
        $customerObjectModel = $this->customerRepository->getById($customer->getId());
        $this->smCustomerResourceRepo->addIncompleteAddress($customerObjectModel);

        $this->refresh($customer);

        return $this->_redirect($this->_loginPostRedirect());
    }

    /**
     * @param $userProfile
     * @param $type
     *
     * @return bool|Customer|mixed
     * @throws Exception
     * @throws LocalizedException
     */
    public function createCustomerProcess($userProfile, $type)
    {
        $name = explode(' ', $userProfile->displayName ?: __('New User'));
        $user = array_merge(
            [
                'email'      => $this->getRequest()->getParam('email'),
                'firstname'  => $userProfile->firstName ?: (array_shift($name) ?: $userProfile->identifier),
                'lastname'   => $userProfile->lastName ?: (array_shift($name) ?: $userProfile->identifier),
                'telephone'  => isset($userProfile->phone) ? $userProfile->phone : '88888888',
                'city'       => isset($userProfile->city) ? $userProfile->city : '',
                'district'   => isset($userProfile->district) ? $userProfile->district : '',
                'identifier' => $userProfile->identifier,
                'type'       => $type,
                'password'   => isset($userProfile->password) ? $userProfile->password : null
            ],
            $this->getUserData($userProfile)
        );

        return $this->createCustomer($user, $type);
    }
}
