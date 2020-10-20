<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_TodayDeal
 *
 * Date: April, 2 2020
 * Time: 1:07 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\TodayDeal\Setup\Patch\Data;

use SM\GTM\Helper\Data as GtmHelper;

class AddGtmConfig implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    const VERSION = '0.0.1';

    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    protected $setup;

    /**
     * AddGtmConfig constructor.
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     */
    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup
    ) {
        $this->setup = $setup;
    }

    public function apply()
    {
        $this->setup->startSetup();

        $conn = $this->setup->getConnection();
        $table = $this->setup->getTable('core_config_data');
        $configPath = 'sm_gtm/gtm_variables/variables';
        $select = $conn->select()->from($table)->where("path = '$configPath'");
        $data = $conn->fetchAssoc($select);
        foreach ($data as $item) {
            try {
                $value = \Zend_Json_Decoder::decode($item['value']);
                $value[] = [
                    'frontend_handler' => 'default',
                    'event_trigger'    => \Zend_Json_Encoder::encode(
                        [
                            'event'    => 'click',
                            'selector' => '[data-gtm-event="' . GtmHelper::PROMO_VIEW_ALL_EVENT_NAME . '"]'
                        ]
                    ),
                    'gtm_key'          => GtmHelper::PROMO_VIEW_ALL_EVENT_NAME,
                    'template'         => \Zend_Json_Encoder::encode(
                        [
                            'event'          => GtmHelper::PROMO_VIEW_ALL_EVENT_NAME,
                            'uniqueUserID'   => '<%= customer.uniqueUserID; %>',
                            'userID'         => '<%= customer.userID; %>',
                            'customerID'     => '<%= customer.customerID; %>',
                            'customerType'   => '<%= customer.customerType; %>',
                            'loyalty'        => '<%= customer.loyalty; %>',
                            'customerStatus' => '<%= customer.customerStatus; %>',
                            'loginType'      => '<%= customer.loginType; %>',
                            'store_name'     => '<%= customer.storeName; %>',
                            'store_ID'       => '<%= customer.storeID; %>'
                        ]
                    )
                ];
                $item['value'] = \Zend_Json_Encoder::encode($value);

                $conn->insertOnDuplicate($table, $item, ['value']);
            } catch (\Zend_Json_Exception $e) {
                var_dump($e->getMessage());
                continue;
            }
        }

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
