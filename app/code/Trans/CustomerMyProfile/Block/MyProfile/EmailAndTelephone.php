<?php
/**
 * @category Trans
 * @package  Trans_CustomerMyProfile
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Dwi Septha Kurniawan <septha.kurniawan@transdigital.co.id>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\CustomerMyProfile\Block\MyProfile;

/**
 * Class EmailAndTelephone
 */
class EmailAndTelephone extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var \Magento\Customer\Api\AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * EmailAndTelephone constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Customer\Api\AccountManagementInterface $accountManagement
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Customer\Api\AccountManagementInterface $accountManagement,
        \Magento\Eav\Model\Config $eavConfig,
        array $data = []
    ) {
        $this->accountManagement = $accountManagement;
        $this->currentCustomer = $currentCustomer;
        $this->eavConfig = $eavConfig;
        parent::__construct($context, $data);
    }

    /**
     * Returns the Magento Customer Model for this block
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    public function getCustomer()
    {
        try {
            return $this->currentCustomer->getCustomer();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get telephone value
     *
     * @return string
     */
    public function getTelephone()
    {
        $telephone = '-';
        $customer = $this->getCustomer();
        if ($customer->getCustomAttribute('telephone')) {
            $telephone = $customer->getCustomAttribute('telephone')->getValue();
            $telephone = '08' . preg_replace("/^(^\+628|^628|^08|^8)/", '', $telephone);
        }

        return $telephone;
    }

    /**
     * Get email value
     *
     * @return string
     */
    public function getEmail()
    {
        $email = '-';
        $customer = $this->getCustomer();
        if ($customer->getEmail()) {
            $email = $customer->getEmail();
        }

        return $email;
    }

    /**
     * Get verified email
     *
     * @return string
     */
    public function getVerifiedEmail()
    {
        $statusEmail = __('Unverified');
        if ($this->isVerifiedEmail()) {
            $statusEmail = __('Verified');
        }

        return $statusEmail;
    }

    /**
     * @return bool
     */
    public function isVerifiedEmail()
    {
        $customer = $this->getCustomer();
        if ($customer->getCustomAttribute('is_verified_email')) {
            if ($customer->getCustomAttribute('is_verified_email')->getValue() == 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $isVerified
     *
     * @return string
     */
    public function getVerifyClass($isVerified)
    {
        if ($isVerified) {
            return 'verified';
        }

        return 'unverified';
    }
}
