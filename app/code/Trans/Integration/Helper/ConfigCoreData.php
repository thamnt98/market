<?php
/**
 * @category Trans
 * @package  Trans_Integration
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.co.id>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Integration\Helper;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Config
 */
class ConfigCoreData extends \Magento\Framework\App\Helper\AbstractHelper
{
    const CHANNEL_STORE_UPDATES = "integration_channel/store_updates";

    protected $configWriter;
    protected $scopeCoreConfig;

    public function __construct(
        WriterInterface $configWriter
        ,ScopeConfigInterface $scopeCoreConfig
    ) {
        $this->configWriter = $configWriter;
        $this->scopeCoreConfig = $scopeCoreConfig;
    }

    /**
     * @param $value
     */
    public function setStoreUpdates($value) {
        $this->setCoreConfigValue(self::CHANNEL_STORE_UPDATES,$value);
    }

    public function getStoreUpdates(){
        return $this->scopeCoreConfig->getValue(self::CHANNEL_STORE_UPDATES, 'store');
    }

    /**
     * @param $path
     * @param $value
     */
    public function setCoreConfigValue($path,$value){
        $this->configWriter->save($path, $value, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeId = 0);
    }

}