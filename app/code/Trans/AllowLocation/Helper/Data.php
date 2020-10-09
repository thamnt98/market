<?php 

/**
 * @category Trans
 * @package  Trans_AllowLocation
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\AllowLocation\Helper;

use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * General Configuration
     */
    const XML_PATH_ENABLED ='allowloc/generaltopsetting/enabled';
    const XML_SECRET_KEY ='allowloc/generaltopsetting/secretkey';

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

    function __construct(
       \Magento\Framework\App\Helper\Context $context,
       \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
       \Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Get Store Id 
     *
     * @return bool
     */
    public function getStoreid()
    {
        return $this->storeManager->getStore()->getId();
    }
    
    /**
     * Enable config OTP Admin
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreid());
    }

    /**
     * Get Secret Key
     *
     * @return string
     */
    public function getSecretKey()
    {
        return $this->scopeConfig->getValue(self::XML_SECRET_KEY,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreid());
    }
}