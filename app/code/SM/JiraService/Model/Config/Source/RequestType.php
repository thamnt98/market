<?php

/**
 * @category SM
 * @package SM_Help
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author      Chinhvd <chinhvd@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\JiraService\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class RequestType implements OptionSourceInterface
{
    const URL_GET_REQUEST_TYPE = '/rest/servicedeskapi/requesttype';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    private $curl;

    /**
     * JiraRepository constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\HTTP\Client\Curl $curl
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->curl = $curl;
    }

    public function toOptionArray()
    {
        $result = [];
        try {
            $serviceDesk = $this->getServiceDesk();
            foreach ($serviceDesk as $value) {
                $result[] = [
                    'value' => $value['id'],
                    'label' => $value['name']
                ];
            }
            return $result;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     *  Get Service Desk.
     * @return array
     */
    protected function getServiceDesk()
    {
        $username = $this->scopeConfig->getValue('sm_jira/account/username');
        $requestUrl = $this->scopeConfig->getValue('sm_jira/ticket/domain') . self::URL_GET_REQUEST_TYPE;
        $apiToken = $this->scopeConfig->getValue('sm_jira/account/api_token');
        $this->curl->setHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X-ExperimentalApi' => 'opt-in'
        ]);
        $this->curl->setCredentials($username, $apiToken);
        $this->curl->get($requestUrl);

        $response = $this->curl->getBody();
        $jiraCustomer = json_decode($response, true);
        return $jiraCustomer['values'];
    }
}
