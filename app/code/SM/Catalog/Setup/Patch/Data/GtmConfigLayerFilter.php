<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Catalog
 *
 * Date: April, 10 2020
 * Time: 11:16 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Catalog\Setup\Patch\Data;

class GtmConfigLayerFilter implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
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

        $handler = 'default';
        $key = \SM\GTM\Helper\Data::LAYER_FILTER_CLICK_EVENT_NAME;
        $keyData = \SM\GTM\Helper\Data::LAYER_FILTER_KEY_NAME;
        $trigger = [
            'event'    => 'click',
            'selector' => '[data-gtm-event="' . $key . '"]'
        ];
        $templateData = [
            'event'           => $key,
            'uniqueUserID'    => '<%= customer.uniqueUserID; %>',
            'userID'          => '<%= customer.userID; %>',
            'customerID'      => '<%= customer.customerID; %>',
            'customerType'    => '<%= customer.customerType; %>',
            'loyalty'         => '<%= customer.loyalty; %>',
            'customerStatus'  => '<%= customer.customerStatus; %>',
            'loginType'       => '<%= customer.loginType; %>',
            'filter_category' => '<%= ' . $keyData . '.name; %>',
            'filter_name'     => '<%= ' . $keyData . '.option; %>',
            'store_name'      => '<%= customer.storeName; %>',
            'store_ID'        => '<%= customer.storeID; %>'
        ];

        $this->gtmSetup->add($handler, $trigger, $key, $templateData);

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
