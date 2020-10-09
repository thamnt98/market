<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Catalog
 *
 * Date: April, 04 2020
 * Time: 4:54 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Catalog\Setup\Patch\Data;

class GtmConfigLatestSearch extends \SM\GTM\Model\Setup\AbstractUpdateConfig
{
    /**
     * @throws \Zend_Json_Exception
     */
    public function apply()
    {
        $this->setup->startSetup();

        $handler = 'default';
        $key = \SM\GTM\Helper\Data::SEARCH_LATEST_DELETE_ALL_CLICK_EVENT_NAME;
        $trigger = [
            'event'    => 'click',
            'selector' => '[data-gtm-event="' . $key . '"]'
        ];
        $templateData = [
            'event'          => 'deleteAll_atest_search',
            'uniqueUserID'   => '<%= customer.uniqueUserID; %>',
            'userID'         => '<%= customer.userID; %>',
            'customerID'     => '<%= customer.customerID; %>',
            'customerType'   => '<%= customer.customerType; %>',
            'loyalty'        => '<%= customer.loyalty; %>',
            'customerStatus' => '<%= customer.customerStatus; %>',
            'loginType'      => '<%= customer.loginType; %>',
            'store_name'     => '<%= customer.storeName; %>',
            'store_ID'       => '<%= customer.storeID; %>'
        ];

        $this->gtmSetup->add($handler, $trigger, $key, $templateData);

        $templateData['query'] = '<%= ' . \SM\GTM\Helper\Data::SEARCH_KEY_NAME . '.query; %>';
        $key = \SM\GTM\Helper\Data::SEARCH_LATEST_DELETE_CLICK_EVENT_NAME;
        $trigger['selector'] = '[data-gtm-event="' . $key . '"]';
        $this->gtmSetup->add($handler, $trigger, $key, $templateData);

        $key = \SM\GTM\Helper\Data::SEARCH_LATEST_CLICK_EVENT_NAME;
        $templateData['event'] = $key;
        $trigger['selector'] = '[data-gtm-event="' . $key . '"]';
        $this->gtmSetup->add($handler, $trigger, $key, $templateData);

        $this->setup->endSetup();
    }
}
