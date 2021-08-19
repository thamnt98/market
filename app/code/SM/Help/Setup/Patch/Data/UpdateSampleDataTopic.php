<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Help\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\PatchInterface;

/**
 * Class UpdateSampleDataTopic
 * @package SM\Help\Setup\Patch\Data
 */
class UpdateSampleDataTopic implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    protected $setup;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * CreateSampleDataTopic constructor.
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->setup = $setup;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $this->setup->startSetup();

        $conn = $this->setup->getConnection();

        // Create sample data sm_help_topic_store
        $table = $this->setup->getTable('sm_help_topic_store');

        $stores = $this->storeManager->getStores();
        foreach ($stores as $store) {
            $data = $this->getSampleTopicStoreAttributes($store->getId());
            foreach ($data as $item) {
                try {
                    $conn->insertOnDuplicate($table, $item);
                } catch (\Zend_Json_Exception $e) {
                    continue;
                }
            }
        }

        $this->setup->endSetup();
    }

    /**
     * @param int $storeId
     * @return array[]
     */
    private function getSampleTopicStoreAttributes($storeId)
    {
        return [
            [
                'store_id'    => $storeId,
                'topic_id'    => 1,
                'name'        => 'Root Topic',
                'status'      => 1,
                'description' => 'Root Topic Default',
            ],
            [
                'store_id'    => $storeId,
                'topic_id'    => 2,
                'name'        => 'My Orders',
                'status'      => 1,
                'description' => 'Get to know more about your orders',
            ],
            [
                'store_id'    => $storeId,
                'topic_id'    => 3,
                'name'        => 'Return & Refund',
                'status'      => 1,
                'description' => 'Learn how we process your return & refund',
            ],
            [
                'store_id'    => $storeId,
                'topic_id'    => 4,
                'name'        => 'My Account',
                'status'      => 1,
                'description' => 'Find all about your account customization',
            ],
            [
                'store_id'    => $storeId,
                'topic_id'    => 5,
                'name'        => 'Payment',
                'status'      => 1,
                'description' => 'Check out the available methods & how to',
            ],
            [
                'store_id'    => $storeId,
                'topic_id'    => 6,
                'name'        => 'Promo & Vouchers',
                'status'      => 1,
                'description' => 'Learn more about our vouchers & promos',
            ],
            [
                'store_id'    => $storeId,
                'topic_id'    => 7,
                'name'        => 'Buying Guide',
                'status'      => 1,
                'description' => 'Let us help you shop in METRO',
            ],
            [
                'store_id'    => $storeId,
                'topic_id'    => 8,
                'name'        => 'About Us',
                'status'      => 1,
                'description' => 'Learn more about METRO',
            ],
            [
                'store_id'    => $storeId,
                'topic_id'    => 9,
                'name'        => 'Delivery & Pick Up in Store',
                'status'      => 1,
                'description' => 'Know our delivery options in METRO',
            ],
        ];
    }
}
