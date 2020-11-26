<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_VesMenu
 *
 * Date: April, 04 2020
 * Time: 3:15 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\VesMenu\Setup\Patch\Data;

class GtmConfigCategoryClick implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    const VERSION = '0.0.2';

    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    protected $setup;

    /**
     * @var \SM\GTM\Helper\Setup\UpdateConfig
     */
    protected $gtmSetup;

    /**
     * AddGtmConfig constructor.
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \SM\GTM\Helper\Setup\UpdateConfig                 $gtmSetup
     */
    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \SM\GTM\Helper\Setup\UpdateConfig $gtmSetup
    ) {
        $this->setup = $setup;
        $this->gtmSetup = $gtmSetup;
    }

    /**
     * @throws \Zend_Json_Exception
     */
    public function apply()
    {
        $this->setup->startSetup();

//        $handler = 'default';
//        $key = 'categories_click';
//        $trigger = [
//            'event'    => 'click',
//            'selector' => '[data-gtm-event="' . $key . '"]'
//        ];
//        $templateDate = [
//            'event'          => $key,
//            'uniqueUserID'   => '<%= customer.uniqueUserID; %>',
//            'userID'         => '<%= customer.userID; %>',
//            'customerID'     => '<%= customer.customerID; %>',
//            'customerType'   => '<%= customer.customerType; %>',
//            'loyalty'        => '<%= customer.loyalty; %>',
//            'customerStatus' => '<%= customer.customerStatus; %>',
//            'loginType'      => '<%= customer.loginType; %>',
//            'menu_category'  => '<%= category.menu_category; %>',
//            'store_name'     => '<%= customer.storeName; %>',
//            'store_ID'       => '<%= customer.storeID; %>'
//        ];
//
//        $this->gtmSetup->add($handler, $trigger, $key, $templateDate);

        $this->setup->endSetup();
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}
