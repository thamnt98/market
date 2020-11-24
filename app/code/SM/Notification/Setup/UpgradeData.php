<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: October, 05 2020
 * Time: 2:05 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements \Magento\Framework\Setup\UpgradeDataInterface
{
    /**
     * Function install
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $this->truncateTable($setup);
        }

        $setup->endSetup();
    }

    public function truncateTable(ModuleDataSetupInterface $setup)
    {
        $table = $setup->getTable(\SM\Notification\Model\ResourceModel\Push::TABLE_NAME);
        if ($setup->tableExists($table)) {
            $setup->getConnection()->truncateTable($table);
        }

        $table = $setup->getTable(\SM\Notification\Model\ResourceModel\Sms::TABLE_NAME);
        if ($setup->tableExists($table)) {
            $setup->getConnection()->truncateTable($table);
        }

        $table = $setup->getTable(\SM\Notification\Model\ResourceModel\Email::TABLE_NAME);
        if ($setup->tableExists($table)) {
            $setup->getConnection()->truncateTable($table);
        }

        $table = $setup->getTable(\SM\Notification\Model\ResourceModel\CustomerMessage::TABLE_NAME);
        if ($setup->tableExists($table)) {
            $setup->getConnection()->truncateTable($table);
        }

        $table = $setup->getTable(\SM\Notification\Model\ResourceModel\TriggerEvent::TABLE_NAME);
        if ($setup->tableExists($table)) {
            $setup->getConnection()->truncateTable($table);
        }

        $table = $setup->getTable(\SM\Notification\Model\ResourceModel\Notification::TABLE_NAME);
        if ($setup->tableExists($table)) {
            $setup->getConnection()->truncateTable($table);
        }

        $table = $setup->getTable(\SM\Customer\Model\ResourceModel\CustomerDevice::TABLE_NAME);
        if ($setup->tableExists($table)) {
            $setup->getConnection()->truncateTable($table);
        }
    }
}