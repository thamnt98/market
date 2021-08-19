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
 * Class CreateSampleDataTopic
 * @package SM\Help\Setup\Patch\Data
 */
class CreateSampleDataTopic implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    protected $setup;

    /**
     * CreateSampleDataTopic constructor.
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     */
    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup
    ) {
        $this->setup = $setup;
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

        // Create sample data sm_help_topic
        $table = $this->setup->getTable('sm_help_topic');

        $data = $this->getSampleTopicAttributes();
        foreach ($data as $item) {
            try {
                $conn->insertOnDuplicate($table, $item);
            } catch (\Zend_Json_Exception $e) {
                continue;
            }
        }

        // Create sample data sm_help_topic_store
        $table = $this->setup->getTable('sm_help_topic_store');

        $data = $this->getSampleTopicStoreAttributes();
        foreach ($data as $item) {
            try {
                $conn->insertOnDuplicate($table, $item);
            } catch (\Zend_Json_Exception $e) {
                continue;
            }
        }

        $this->setup->endSetup();
    }

    /**
     * @return array[]
     */
    private function getSampleTopicAttributes()
    {
        return [
            [
                'topic_id'  => 1,
                'url_key'   => '',
                'path'      => '1',
                'level'     => 0,
                'position'  => 0,
                'parent_id' => 0,
            ],
            [
                'topic_id'  => 2,
                'url_key'   => 'my-orders',
                'path'      => '1/2',
                'level'     => 1,
                'position'  => 8,
                'parent_id' => 1,
            ],
            [
                'topic_id'  => 3,
                'url_key'   => 'return-refund',
                'path'      => '1/3',
                'level'     => 1,
                'position'  => 7,
                'parent_id' => 1,
            ],
            [
                'topic_id'  => 4,
                'url_key'   => 'my-account',
                'path'      => '1/4',
                'level'     => 1,
                'position'  => 6,
                'parent_id' => 1,
            ],
            [
                'topic_id'  => 5,
                'url_key'   => 'payment',
                'path'      => '1/5',
                'level'     => 1,
                'position'  => 5,
                'parent_id' => 1,
            ],
            [
                'topic_id'  => 6,
                'url_key'   => 'promo-vouchers',
                'path'      => '1/6',
                'level'     => 1,
                'position'  => 4,
                'parent_id' => 1,
            ],
            [
                'topic_id'  => 7,
                'url_key'   => 'buying-guide',
                'path'      => '1/7',
                'level'     => 1,
                'position'  => 3,
                'parent_id' => 1,
            ],
            [
                'topic_id'  => 8,
                'url_key'   => 'about-us',
                'path'      => '1/8',
                'level'     => 1,
                'position'  => 2,
                'parent_id' => 1,
            ],
            [
                'topic_id'  => 9,
                'url_key'   => 'delivery-pick-up-in-store',
                'path'      => '1/9',
                'level'     => 1,
                'position'  => 1,
                'parent_id' => 1,
            ],
        ];
    }

    /**
     * @return array[]
     */
    private function getSampleTopicStoreAttributes()
    {
        return [
            [
                'store_id'    => 0,
                'topic_id'    => 1,
                'name'        => 'Root Topic',
                'status'      => 1,
                'description' => 'Root Topic Default',
            ],
            [
                'store_id'    => 0,
                'topic_id'    => 2,
                'name'        => 'My Orders',
                'status'      => 1,
                'description' => 'Get to know more about your orders',
            ],
            [
                'store_id'    => 0,
                'topic_id'    => 3,
                'name'        => 'Return & Refund',
                'status'      => 1,
                'description' => 'Learn how we process your return & refund',
            ],
            [
                'store_id'    => 0,
                'topic_id'    => 4,
                'name'        => 'My Account',
                'status'      => 1,
                'description' => 'Find all about your account customization',
            ],
            [
                'store_id'    => 0,
                'topic_id'    => 5,
                'name'        => 'Payment',
                'status'      => 1,
                'description' => 'Check out the available methods & how to',
            ],
            [
                'store_id'    => 0,
                'topic_id'    => 6,
                'name'        => 'Promo & Vouchers',
                'status'      => 1,
                'description' => 'Learn more about our vouchers & promos',
            ],
            [
                'store_id'    => 0,
                'topic_id'    => 7,
                'name'        => 'Buying Guide',
                'status'      => 1,
                'description' => 'Let us help you shop in METRO',
            ],
            [
                'store_id'    => 0,
                'topic_id'    => 8,
                'name'        => 'About Us',
                'status'      => 1,
                'description' => 'Learn more about METRO',
            ],
            [
                'store_id'    => 0,
                'topic_id'    => 9,
                'name'        => 'Delivery & Pick Up in Store',
                'status'      => 1,
                'description' => 'Know our delivery options in METRO',
            ],
        ];
    }
}
