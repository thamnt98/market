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

use \Magento\Framework\Db\Ddl\Table;
use \Magento\Framework\Setup\InstallSchemaInterface;
use \Magento\Framework\Setup\ModuleContextInterface;
use \Magento\Framework\Setup\SchemaSetupInterface;
use \Trans\MasterPayment\Api\Data\MasterPaymentInterface;

/**
 * {@inheritdoc}
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
class InstallSchema implements InstallSchemaInterface {

	/**
	 * {@inheritdoc}
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
		$setup->startSetup();

		$this->createMasterPaymentTable($setup);

		$setup->endSetup();
	}

	/**
	 * Create table master_payment
	 *
	 * @param $setup
	 */
	private function createMasterPaymentTable($setup) {
		$table = $setup->getConnection()
			->newTable($setup->getTable(MasterPaymentInterface::TABLE_NAME))
			->addColumn(
				MasterPaymentInterface::ID,
				Table::TYPE_INTEGER,
				null,
				['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
				'Id'
			)
			->addColumn(
				MasterPaymentInterface::PAYMENT_ID,
				Table::TYPE_TEXT,
				24,
				['nullable' => false],
				ucfirst(MasterPaymentInterface::PAYMENT_ID)
			)
			->addColumn(
				MasterPaymentInterface::PAYMENT_TITLE,
				Table::TYPE_TEXT,
				255,
				['nullable' => false],
				ucfirst(MasterPaymentInterface::PAYMENT_TITLE)
			)
			->addColumn(
				MasterPaymentInterface::PAYMENT_METHOD,
				Table::TYPE_TEXT,
				50,
				['nullable' => true],
				ucfirst(MasterPaymentInterface::PAYMENT_METHOD)
			)
			->addColumn(
				MasterPaymentInterface::PAYMENT_TERMS,
				Table::TYPE_TEXT,
				24,
				['nullable' => true],
				ucfirst(MasterPaymentInterface::PAYMENT_TERMS)
			)
			->addColumn(
				MasterPaymentInterface::CREATED_AT,
				\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
				null,
				['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
				'Created Time'
			)
			->addColumn(
				MasterPaymentInterface::UPDATED_AT,
				\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
				null,
				['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
				'Updated Time'
			)
			->setComment('MasterPayment Response Table');

		$setup->getConnection()->createTable($table);
	}
}
