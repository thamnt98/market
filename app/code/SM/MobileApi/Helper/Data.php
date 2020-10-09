<?php

namespace SM\MobileApi\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Data
 * @package SM\MobileApi\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig  = $context->getScopeConfig();
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Is need bypass session settings
     *
     * @return bool
     */
    public function isBypassSession()
    {
        $pathInfo = $this->_request->getPathInfo();
        if (strpos($pathInfo, '/jm360/') !== false || strpos($pathInfo, '/japi/') !== false) {
            return true;
        }

        return false;
    }

    /**
     * Proxy for \Magento\Framework\App\Config\ScopeConfigInterface::getValue
     *
     * @param string $path
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getConfigValue($path)
    {
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );
    }

    /**
     * Proxy for \Magento\Framework\App\Config\ScopeConfigInterface::isSetFlag
     *
     * @param string $path
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isSetConfigFlag($path)
    {
        return $this->scopeConfig->isSetFlag(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );
    }
}
