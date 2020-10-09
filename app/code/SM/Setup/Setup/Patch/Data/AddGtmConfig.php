<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Setup
 *
 * Date: March, 21 2020
 * Time: 4:49 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Setup\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddGtmConfig implements DataPatchInterface
{
    const VERSION = '2.0.0';

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
                            'selector' => '.action.viewcart'
                        ]
                    ),
                    'gtm_key'          => 'go_to_cart',
                    'template'         => \Zend_Json_Encoder::encode(
                        [
                            'event'            => 'go_to_cart',
                            'uniqueUserID'     => '<%= customer.uniqueUserID; %>',
                            'userID'           => '<%= customer.userID; %>',
                            'customerID'       => '<%= customer.customerID; %>',
                            'customerType'     => '<%= customer.customerType; %>',
                            'loyalty'          => '<%= customer.loyalty; %>',
                            'customerStatus'   => '<%= customer.customerStatus; %>',
                            'loginType'        => '<%= customer.loginType; %>',
                            'timestamp'        => "<%= moment().format('DD/MM/YYYY HH:mm:ss'); %>",
                            'cartID'           => '<%= quote.cart_id; %>',
                            'email'            => '<%= quote.email; %>',
                            'product_quantity' => '<%= quote.product_quantity; %>',
                            'store_name'       => '<%= customer.storeName; %>',
                            'store_ID'         => '<%= customer.storeID; %>'
                        ]
                    )
                ];
                $value[] = [
                    'frontend_handler' => 'default',
                    'event_trigger'    => \Zend_Json_Encoder::encode(
                        [
                            'event'    => 'click',
                            'selector' => '.action.showcart'
                        ]
                    ),
                    'gtm_key'          => 'cart_list',
                    'template'         => \Zend_Json_Encoder::encode(
                        [
                            'event'            => 'cart_list',
                            'uniqueUserID'     => '<%= customer.uniqueUserID; %>',
                            'userID'           => '<%= customer.userID; %>',
                            'customerID'       => '<%= customer.customerID; %>',
                            'customerType'     => '<%= customer.customerType; %>',
                            'loyalty'          => '<%= customer.loyalty; %>',
                            'customerStatus'   => '<%= customer.customerStatus; %>',
                            'loginType'        => '<%= customer.loginType; %>',
                            'timestamp'        => "<%= moment().format('DD/MM/YYYY HH:mm:ss'); %>",
                            'cartID'           => '<%= quote.cart_id; %>',
                            'email'            => '<%= quote.email; %>',
                            'product_quantity' => '<%= quote.product_quantity; %>',
                            'store_name'       => '<%= customer.storeName; %>',
                            'store_ID'         => '<%= customer.storeID; %>'
                        ]
                    ),
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
