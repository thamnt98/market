<?php
/**
 * @category Trans
 * @package  trans_MasterPayment
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\MasterPayment\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Trans\MasterPayment\Api\Data\MasterPaymentMatrixAdjustmentInterface;

/**
 * @SuppressWarnings(PHPMD)
 */
class UpgradeSchema implements UpgradeSchemaInterface {
	public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {
		$setup->startSetup();

		if (version_compare($context->getVersion(), '0.0.2', '<')) {
			$this->createMasterPaymentMatrixAdjustmentTable($setup);
		}

		$setup->endSetup();
	}

	/**
	 * Create table master_payment_matrix_adjustment
	 *
	 * @param $setup
	 */
	private function createMasterPaymentMatrixAdjustmentTable($setup) {
		$table = $setup->getConnection()
			->newTable($setup->getTable(MasterPaymentMatrixAdjustmentInterface::TABLE_NAME))
			->addColumn(
				MasterPaymentMatrixAdjustmentInterface::ID,
				Table::TYPE_INTEGER,
				null,
				['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
				'Id'
			)
			->addColumn(
				MasterPaymentMatrixAdjustmentInterface::TRANSACTION_NO,
				Table::TYPE_TEXT,
				50,
				['nullable' => false],
				ucfirst(MasterPaymentMatrixAdjustmentInterface::TRANSACTION_NO)
			)
			->addColumn(
				MasterPaymentMatrixAdjustmentInterface::PAID_AMOUNT,
				Table::TYPE_DECIMAL,
				'20,4',
				['nullable' => false],
				ucfirst(MasterPaymentMatrixAdjustmentInterface::PAID_AMOUNT)
			)
			->addColumn(
				MasterPaymentMatrixAdjustmentInterface::REFUND_AMOUNT,
				Table::TYPE_DECIMAL,
				'20,4',
				['nullable' => true],
				ucfirst(MasterPaymentMatrixAdjustmentInterface::REFUND_AMOUNT)
			)
			->addColumn(
				MasterPaymentMatrixAdjustmentInterface::STATUS,
				Table::TYPE_TEXT,
				5,
				['nullable' => true],
				ucfirst(MasterPaymentMatrixAdjustmentInterface::STATUS)
			)
			->addColumn(
				MasterPaymentMatrixAdjustmentInterface::MESSAGE,
				Table::TYPE_TEXT,
				255,
				['nullable' => true],
				ucfirst(MasterPaymentMatrixAdjustmentInterface::MESSAGE)
			)
			->addColumn(
				MasterPaymentMatrixAdjustmentInterface::CREATED_AT,
				\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
				null,
				['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
				'Created Time'
			)
			->addColumn(
				MasterPaymentMatrixAdjustmentInterface::UPDATED_AT,
				\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
				null,
				['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
				'Updated Time'
			)
			->setComment('Master Payment Matrix Adjustment Table');

		$setup->getConnection()->createTable($table);
	}
}