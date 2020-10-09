<?php
/**
 * @category Trans
 * @package  Trans_IntegrationNotification
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.co.id>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationNotification\Helper;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 */
class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Constant config path
     * Notification System
     */
    const NOTIFICATION_ENABLED = 'notification/general/enabled';
    const NOTIFICATION_APP_KEY = 'notification/general/key';
    const NOTIFICATION_IS_PRODUCTION = 'notification/general/is_production';
    const NOTIFICATION_URL_DEV = 'notification/base_url/url_dev';
    const NOTIFICATION_URL_PROD = 'notification/base_url/url_prod';
    const NOTIFICATION_PUBLISH_URL = 'notification/base_url/publish_url';

    /**
     * @var \Trans\Integration\Api\IntegrationChannelRepositoryInterface
     */
    protected $channelRepository;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Trans\Integration\Api\IntegrationChannelRepositoryInterface $channelRepository
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Trans\Integration\Api\IntegrationChannelRepositoryInterface $channelRepository
    ) {
        parent::__construct($context);
        $this->channelRepository = $channelRepository;
    }
    
    /**
     * Get config value by path
     *
     * @param string $path
     * @return mixed
     */
    public function getConfigValue($path)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }

    /**
     * is env production
     *
     * @return bool
     */
    public function isProduction()
    {
        return $this->getConfigValue(self::NOTIFICATION_IS_PRODUCTION);
    }

    /**
     * is static otp enabled
     *
     * @return bool
     */
    public function isEnable()
    {
        return $this->getConfigValue(self::NOTIFICATION_ENABLED);
    }

    /**
     * get static otp
     *
     * @return array
     */
    public function getAppKey()
    {
        return $this->getConfigValue(self::NOTIFICATION_APP_KEY);
    }

    /**
     * get api base url
     *
     * @return string
     */
    public function getApiBaseUrl()
    {
        $channelId = $this->getConfigValue(self::NOTIFICATION_URL_DEV);
        
        if ($this->isProduction()) {
            $channelId = $this->getConfigValue(self::NOTIFICATION_URL_PROD);
        }

        try {
            $channel = $this->channelRepository->getById($channelId);
            return $channel->getUrl();
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    /**
     * get API publish URL
     *
     * @return string
     */
    public function getApiPublishUrl()
    {
        $baseUrl = $this->getApiBaseUrl();

        if ($baseUrl) {
            $publishUrl = $this->getConfigValue(self::NOTIFICATION_PUBLISH_URL);
            return $baseUrl . $publishUrl;
        }
    }
}
