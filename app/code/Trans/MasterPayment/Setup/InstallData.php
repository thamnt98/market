<?php
/**
 * @category Trans
 * @package  Trans_MasterPayment
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\MasterPayment\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Trans\MasterPayment\Api\Data\MasterPaymentInterface;

class InstallData implements InstallDataInterface {

	public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
		$setup->startSetup();

		$this->createMasterPaymentData($setup);

		$setup->endSetup();
	}

	/**
	 * Insert data to table Operator List
	 * @param $installer
	 */
	public function createMasterPaymentData($setup) {
		$tableName = $setup->getTable(MasterPaymentInterface::TABLE_NAME);
		if ($setup->getConnection()->isTableExists($tableName) == true) {
			$data = [
				["1010000000", "Credit Card and Debit Card Full Payment", "sprint_allbankfull_cc", ""],
				["1008000012", "BCA VA", "sprint_bca_va", ""],
				["1008000033", "Permata VA", "sprint_permata_va", ""],
				["1010001012", "Credit Card Bank Central Asia - Installment 3 Months", "sprint_bca_cc", "3"],
				["1010002012", "Credit Card Bank Central Asia - Installment 6 Months", "sprint_bca_cc", "6"],
				["1010004012", "Credit Card Bank Central Asia - Installment 12 Months", "sprint_bca_cc", "12"],
			];
			$columns = [MasterPaymentInterface::PAYMENT_ID, MasterPaymentInterface::PAYMENT_TITLE, MasterPaymentInterface::PAYMENT_METHOD, MasterPaymentInterface::PAYMENT_TERMS];
			$setup->getConnection()->insertArray($tableName, $columns, $data);
		}
	}
}