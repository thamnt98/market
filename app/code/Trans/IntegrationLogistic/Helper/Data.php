<?php

/**
 * @category Trans
 * @package  Trans_IntegrationLogistic
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Dwi Septha Kurniawan <septha.kurniawan@transdigital.co.id>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationLogistic\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 */
class Data extends AbstractHelper
{
    /**
     * General Configuration
     */
    const XML_API_TRACKING  = 'integrationtpl/tpl_logistic/apiurl_tracking';
    const XML_CLIENT_ID     = 'integrationtpl/tpl_logistic/client_id';
    const XML_CLIENT_SECRET = 'integrationtpl/tpl_logistic/client_secret';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig  = $scopeConfig;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Get Store Id
     *
     * @return bool
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * Get configuration api get fee
     *
     * @return bool
     */
    public function getApiTracking()
    {
        return $this->scopeConfig->getValue(
            self::XML_API_TRACKING,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
    }

    /**
     * Get configuration client id
     *
     * @return bool
     */
    public function getClientId()
    {
        return $this->scopeConfig->getValue(
            self::XML_CLIENT_ID,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
    }

    /**
     * Get configuration client secret
     *
     * @return bool
     */
    public function getClientSecret()
    {
        return $this->scopeConfig->getValue(
            self::XML_CLIENT_SECRET,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
    }

    // /**
    //  * Get configuration merchant code
    //  *
    //  * @return bool
    //  */
    // public function getMerchantCode() {
    //  return $this->scopeConfig->getValue(
    //      self::XML_MERCHANT_CODE,
    //      ScopeInterface::SCOPE_STORE,
    //      $this->getStoreId()
    //  );
    // }
}
