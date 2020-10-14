<?php
/**
 * @category Trans
 * @package  Trans_IntegrationOrder
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.co.id>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationOrder\Helper;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Trans\IntegrationOrder\Api\Data\IntegrationOrderInterface;

/**
 * Class Integration
 */
class Integration extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var array
     */
    protected $instances = [];

    /**
     * @var \Trans\IntegrationOrder\Logger\Logger
     */
    protected $logger;

    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected $curl;

    /**
     * @var \Trans\IntegrationOrder\Helper\Config
     */
    protected $configHelper;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \Trans\IntegrationOrder\Helper\Config $configHelper
     */ 
   public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Trans\IntegrationOrder\Helper\Config $configHelper,
        \Trans\IntegrationOrder\Helper\Data $dataHelper
    )
    {
        parent::__construct($context);

        $this->curl = $curl;
        $this->configHelper = $configHelper;
        $this->logger = $dataHelper->getLogger();
    }

    /**
     * get oms token
     * 
     * @return string
     */
    public function getToken()
    {
        $token = $this->configHelper->getOmsToken();
        
        if($token) {
            return $token;
        }

        $url = $this->configHelper->getOmsBaseUrl() . $this->configHelper->getOmsLoginApi();
        
        $body['username'] = 'admin@ct.co.id';
        $body['password'] = 'admin';
        
        try {
            $headers = ['dest' => $this->configHelper->getOmsDest()];
            
            $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
            $this->curl->setHeaders($headers);
            $this->curl->post($url, $body);
            $response = $this->curl->getBody();
            $this->logger->info($response);
            
            $obj = json_decode($response);
            if($obj->code == 200) {
                $token = $obj->content->token;
                return $token;
            }
            
        } catch (\Exception $e) {
            $this->logger->info($e);
        }

    } 
}
