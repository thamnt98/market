<?php
/**
 * @category Trans
 * @package  trans_MasterPayment
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.com>
 *
 * Copyright © 2020 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\MasterPayment\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{

    /**
     * Upgrade DB schema for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $masterPaymentTable = $setup->getTable('master_payment');
        if (version_compare($context->getVersion(), '0.0.3', '<')) {
            if ($setup->getConnection()->isTableExists($masterPaymentTable) == true) {
                $data = [
                    ["1010000024", "Credit Card Bank Mega", "sprint_mega_cc", ""],
                    ["1011000024", "Debit Card Bank Mega", "sprint_mega_debit", ""],
                    ["1008000024", "Bank Mega VA", "trans_mepay_va", ""],
                    ["10100000241", "Credit and Debit Card Bank Mega (Bank Mega PG)", "trans_mepay_cc", ""],
                    ["1016000024", "QR Code Bank Mega", "trans_mepay_qris", ""],
                ];
                $columns = ['payment_id', 'payment_title', 'payment_method', 'payment_terms'];
                $setup->getConnection()->insertArray($masterPaymentTable, $columns, $data);
            }
            $setup->endSetup();
        }
    }
}
