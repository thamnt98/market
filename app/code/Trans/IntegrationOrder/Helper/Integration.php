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
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * @var \Trans\IntegrationOrder\Helper\Config
     */
    protected $configHelper;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \Trans\IntegrationOrder\Helper\Config $configHelper
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     */ 
   public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Trans\IntegrationOrder\Helper\Config $configHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Trans\IntegrationOrder\Helper\Data $dataHelper
    )
    {
        parent::__construct($context);

        $this->curl = $curl;
        $this->jsonHelper = $jsonHelper;
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
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        //$token = $this->configHelper->getOmsToken();
        
        // if($token) {
        //     $logger->info("Token dari configHelper");
        //     return $token;
        // }


    try {

        //===== add by @nurakhiri 2021-07-26

        $url = $this->configHelper->getOmsBaseUrl() . $this->configHelper->getOmsLoginApi();

        
        $requestUrl = $this->configHelper->getOmsBaseUrl() . $this->configHelper->getOmsLoginApi();
        $params =
            [
                'username' => "bismar@metroindonesia.com",
                'password' => "Admin123!"
            ];
        $this->curl->setHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X-ExperimentalApi' => 'opt-in',
            'dest' => 'omega',
        ]);
        $this->curl->post($requestUrl, $this->jsonHelper->jsonEncode($params));
        $response = $this->curl->getBody();
        //$response = json_decode($response, true);
            
            $obj = json_decode($response);            
            if($obj->code == 200) {
                $token = $obj->content->token;
                $logger->info("===>> Nurakhiri getToken Baru OMS <<=======");
                $logger->info($token);
                return $token;
            }
            
        } catch (\Exception $e) {
            $logger->info("===>> Nurakhiri getToken Baru ERROR OMS <<=======");
            $logger->info($e);
            $this->logger->info($e);
        }

    } 
}
