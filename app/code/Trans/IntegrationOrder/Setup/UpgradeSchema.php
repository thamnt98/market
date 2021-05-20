<?php
/**
 * @category Trans
 * @package  Trans_IntegrationOrder
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationOrder\Setup;

use Magento\Framework\Db\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use \Trans\IntegrationOrder\Api\Data\IntegrationOrderHistoryInterface;
use \Trans\IntegrationOrder\Api\Data\IntegrationOrderReturnInterface;
use \Trans\IntegrationOrder\Api\Data\IntegrationOrderStatusInterface;
use \Trans\IntegrationOrder\Api\Data\RefundInterface;

class UpgradeSchema implements UpgradeSchemaInterface {
	public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {
		$installer = $setup;

		$installer->startSetup();

		if (version_compare($context->getVersion(), '1.0.1', '<')) {
			$installer->getConnection()->addColumn(
				$installer->getTable('integration_oms_order_payment'),
				'order_id',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => false,
					'comment' => 'order_id',
					'after' => 'reference_number',
				]
			);
		}

		if (version_compare($context->getVersion(), '1.0.2', '<')) {
			$installer->getConnection()->addColumn(
				$installer->getTable('integration_oms_order_item'),
				'quantity_allocated',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'nullable' => false,
					'comment' => 'quantity_allocated',
					'after' => 'qty',
				]
			);
			$installer->getConnection()->addColumn(
				$installer->getTable('integration_oms_order_item'),
				'item_status',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'nullable' => false,
					'comment' => 'item_status',
					'after' => 'quantity_allocated',
				]
			);
		}

		if (version_compare($context->getVersion(), '1.0.4', '<')) {
			$installer->getConnection()->addColumn(
				$installer->getTable('integration_oms_oar'),
				'is_warehouse',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
					'nullable' => false,
					'comment' => 'is_warehouse',
					'after' => 'store_code',
				]
			);
			$installer->getConnection()->addColumn(
				$installer->getTable('integration_oms_order'),
				'customer_email',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => false,
					'comment' => 'customer_email',
					'after' => 'customer_phone_number',
				]
			);

			$installer->getConnection()->addColumn(
				$installer->getTable('integration_oms_order'),
				'receiver_name',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => false,
					'comment' => 'receiver_name',
					'after' => 'customer_email',
				]
			);

			$installer->getConnection()->addColumn(
				$installer->getTable('integration_oms_order'),
				'receiver_phone',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => false,
					'comment' => 'receiver_phone',
					'after' => 'receiver_name',
				]
			);

			$installer->getConnection()->addColumn(
				$installer->getTable('integration_oms_order'),
				'flag_spo',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
					'nullable' => false,
					'comment' => 'flag_spo',
					'after' => 'shipment_type',
				]
			);
			$setup->getConnection()->dropColumn($setup->getTable('integration_oms_order_item'), 'voucher_code');
			$setup->getConnection()->dropColumn($setup->getTable('integration_oms_order_item'), 'promo_id');

			$installer->getConnection()->addColumn(
				$installer->getTable('integration_oms_order_item'),
				'coupon_code',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => false,
					'comment' => 'coupon_code',
					'after' => 'subtotal',
				]
			);

			$installer->getConnection()->addColumn(
				$installer->getTable('integration_oms_order_item'),
				'coupon_value',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
					'nullable' => false,
					'comment' => 'coupon_value',
					'after' => 'coupon_code',
				]
			);

			$installer->getConnection()->addColumn(
				$installer->getTable('integration_oms_order_item'),
				'promotion_type',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => false,
					'comment' => 'promotion_type',
					'after' => 'coupon_value',
				]
			);

			$installer->getConnection()->addColumn(
				$installer->getTable('integration_oms_order_item'),
				'promotion_value',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => false,
					'comment' => 'promotion_value',
					'after' => 'promotion_type',
				]
			);
		}

		if (version_compare($context->getVersion(), '1.0.5', '<')) {
			$installer->getConnection()->addColumn(
				$installer->getTable('integration_oms_order_item'),
				'is_warehouse',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
					'nullable' => false,
					'comment' => 'is_warehouse',
					'after' => 'item_status',
				]
			);
			$setup->getConnection()->dropColumn($setup->getTable('integration_oms_order'), 'flag_spo');

			$installer->getConnection()->addColumn(
				$installer->getTable('integration_oms_order'),
				'flag_spo',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'nullable' => false,
					'comment' => 'flag_spo',
					'after' => 'shipment_type',
				]
			);
		}

		if (version_compare($context->getVersion(), '1.0.6', '<')) {
			// Get table
			$tableName = $installer->getTable(IntegrationOrderHistoryInterface::TABLE_NAME);
			// Check if the table already exists
			if ($installer->getConnection()->isTableExists($tableName) != true) {
				// Create table
				$table = $installer->getConnection()
					->newTable($tableName)
					->addColumn(
						IntegrationOrderHistoryInterface::HISTORY_ID,
						Table::TYPE_INTEGER,
						null,
						[
							'identity' => true,
							'unsigned' => true,
							'nullable' => false,
							'primary' => true,
						],
						ucfirst(IntegrationOrderHistoryInterface::HISTORY_ID)
					)
					->addColumn(
						IntegrationOrderHistoryInterface::REFERENCE_NUMBER,
						Table::TYPE_TEXT,
						null,
						['nullable' => false, 'default' => ''],
						ucfirst(IntegrationOrderHistoryInterface::REFERENCE_NUMBER)
					)
					->addColumn(
						IntegrationOrderHistoryInterface::ORDER_ID,
						Table::TYPE_TEXT,
						null,
						['nullable' => false, 'default' => ''],
						ucfirst(IntegrationOrderHistoryInterface::ORDER_ID)
					)
					->addColumn(
						IntegrationOrderHistoryInterface::AWB_NUMBER,
						Table::TYPE_TEXT,
						null,
						['nullable' => false, 'default' => ''],
						ucfirst(IntegrationOrderHistoryInterface::AWB_NUMBER)
					)
					->addColumn(
						IntegrationOrderHistoryInterface::LOGISTIC_COURIER,
						Table::TYPE_INTEGER,
						null,
						['nullable' => false],
						ucfirst(IntegrationOrderHistoryInterface::LOGISTIC_COURIER)
					)
					->addColumn(
						IntegrationOrderHistoryInterface::FE_STATUS_NO,
						Table::TYPE_TEXT,
						null,
						['nullable' => false, 'default' => ''],
						ucfirst(IntegrationOrderHistoryInterface::FE_STATUS_NO)
					)
					->addColumn(
						IntegrationOrderHistoryInterface::FE_SUB_STATUS_NO,
						Table::TYPE_TEXT,
						null,
						['nullable' => false, 'default' => ''],
						ucfirst(IntegrationOrderHistoryInterface::FE_SUB_STATUS_NO)
					)
					->addColumn(
						IntegrationOrderHistoryInterface::UPDATED_AT,
						Table::TYPE_TIMESTAMP,
						null,
						['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
						ucfirst(IntegrationOrderHistoryInterface::UPDATED_AT)
					)
					->setComment('Integration Order History')
					->setOption('type', 'InnoDB')
					->setOption('charset', 'utf8');
				$installer->getConnection()->createTable($table);
			}
		}
		if (version_compare($context->getVersion(), '1.0.7', '<')) {
			// Get table
			$tableName = $installer->getTable(IntegrationOrderStatusInterface::TABLE_NAME);
			// Check if the table already exists
			if ($installer->getConnection()->isTableExists($tableName) != true) {
				// Create table
				$table = $installer->getConnection()
					->newTable($tableName)
					->addColumn(
						IntegrationOrderStatusInterface::STATUS_ID,
						Table::TYPE_INTEGER,
						null,
						[
							'identity' => true,
							'unsigned' => true,
							'nullable' => false,
							'primary' => true,
						],
						ucfirst(IntegrationOrderStatusInterface::STATUS_ID)
					)
					->addColumn(
						IntegrationOrderStatusInterface::OMS_STATUS_NO,
						Table::TYPE_INTEGER,
						null,
						['nullable' => false],
						ucfirst(IntegrationOrderStatusInterface::OMS_STATUS_NO)
					)
					->addColumn(
						IntegrationOrderStatusInterface::OMS_ACTION_NO,
						Table::TYPE_INTEGER,
						null,
						['nullable' => false],
						ucfirst(IntegrationOrderStatusInterface::OMS_ACTION_NO)
					)
					->addColumn(
						IntegrationOrderStatusInterface::OMS_SUB_ACTION_NO,
						Table::TYPE_INTEGER,
						null,
						['nullable' => false],
						ucfirst(IntegrationOrderStatusInterface::OMS_SUB_ACTION_NO)
					)
					->addColumn(
						IntegrationOrderStatusInterface::FE_STATUS_NO,
						Table::TYPE_TEXT,
						null,
						['nullable' => false, 'default' => ''],
						ucfirst(IntegrationOrderStatusInterface::FE_STATUS_NO)
					)
					->addColumn(
						IntegrationOrderStatusInterface::FE_STATUS,
						Table::TYPE_TEXT,
						null,
						['nullable' => false, 'default' => ''],
						ucfirst(IntegrationOrderStatusInterface::FE_STATUS)
					)
					->addColumn(
						IntegrationOrderStatusInterface::FE_SUB_STATUS_NO,
						Table::TYPE_TEXT,
						null,
						['nullable' => false, 'default' => ''],
						ucfirst(IntegrationOrderStatusInterface::FE_SUB_STATUS_NO)
					)
					->addColumn(
						IntegrationOrderStatusInterface::FE_SUB_STATUS,
						Table::TYPE_TEXT,
						null,
						['nullable' => false, 'default' => ''],
						ucfirst(IntegrationOrderStatusInterface::FE_SUB_STATUS)
					)
					->addColumn(
						IntegrationOrderStatusInterface::OMS_PAYMENT_STATUS,
						Table::TYPE_TEXT,
						null,
						['nullable' => false, 'default' => ''],
						ucfirst(IntegrationOrderStatusInterface::OMS_PAYMENT_STATUS)
					)
					->addColumn(
						IntegrationOrderStatusInterface::PG_STATUS_NO,
						Table::TYPE_TEXT,
						null,
						['nullable' => false, 'default' => ''],
						ucfirst(IntegrationOrderStatusInterface::PG_STATUS_NO)
					)

					->setComment('Integration Order Status')
					->setOption('type', 'InnoDB')
					->setOption('charset', 'utf8');
				$installer->getConnection()->createTable($table);
			}

		}
		if (version_compare($context->getVersion(), '1.0.8', '<')) {
			$installer->getConnection()->addColumn(
				$installer->getTable('integration_oms_order'),
				'source_channel',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'nullable' => false,
					'comment' => 'source_channel',
					'after' => 'order_source',
				]
			);
		}

		if (version_compare($context->getVersion(), '1.0.9', '<')) {
			$installer->getConnection()->addColumn(
				$installer->getTable('integration_oms_order_item'),
				'sku_basic',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => false,
					'comment' => 'sku_basic',
					'after' => 'sku',
				]
			);
		}

		if (version_compare($context->getVersion(), '1.1.2', '<')) {
			$installer->getConnection()->addColumn(
				$installer->getTable('integration_oms_order_item'),
				'paid_price',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
					'nullable' => false,
					'comment' => 'paid_price',
					'after' => 'sell_price',
				]
			);

			// Get table Refund
			$refundTable = $installer->getTable(RefundInterface::REFUND_TABLE);
			// Check if the table already exists
			if ($installer->getConnection()->isTableExists($refundTable) != true) {
				// Create table
				$table = $installer->getConnection()
					->newTable($refundTable)
					->addColumn(
						RefundInterface::REFUND_ID,
						Table::TYPE_INTEGER,
						null,
						[
							'identity' => true,
							'unsigned' => true,
							'nullable' => false,
							'primary' => true,
						],
						ucfirst(RefundInterface::REFUND_ID)
					)
					->addColumn(
						RefundInterface::ORDER_REF_NUMBER,
						Table::TYPE_TEXT,
						null,
						['nullable' => false, 'default' => ''],
						ucfirst(RefundInterface::ORDER_REF_NUMBER)
					)
					->addColumn(
						RefundInterface::REFUND_TRX_NUMBER,
						Table::TYPE_TEXT,
						null,
						['nullable' => false, 'default' => ''],
						ucfirst(RefundInterface::REFUND_TRX_NUMBER)
					)
					->addColumn(
						RefundInterface::ORDER_ID,
						Table::TYPE_TEXT,
						null,
						['nullable' => false, 'default' => ''],
						ucfirst(RefundInterface::ORDER_ID)
					)
					->addColumn(
						RefundInterface::SKU,
						Table::TYPE_TEXT,
						null,
						['nullable' => false, 'default' => ''],
						ucfirst(RefundInterface::SKU)
					)
					->addColumn(
						RefundInterface::QTY_REFUND,
						Table::TYPE_INTEGER,
						null,
						['nullable' => false],
						ucfirst(RefundInterface::QTY_REFUND)
					)
					->addColumn(
						RefundInterface::AMOUNT_REFUND_SKU,
						Table::TYPE_INTEGER,
						null,
						['nullable' => false],
						ucfirst(RefundInterface::AMOUNT_REFUND_SKU)
					)
					->addColumn(
						RefundInterface::AMOUNT_REFUND_ORDER,
						Table::TYPE_INTEGER,
						null,
						['nullable' => false],
						ucfirst(RefundInterface::AMOUNT_REFUND_ORDER)
					)
					->addColumn(
						RefundInterface::AMOUNT_ORDER,
						Table::TYPE_INTEGER,
						null,
						['nullable' => false],
						ucfirst(RefundInterface::AMOUNT_ORDER)
					)
					->addColumn(
						RefundInterface::AMOUNT_REF_NUMBER,
						Table::TYPE_INTEGER,
						null,
						['nullable' => false],
						ucfirst(RefundInterface::AMOUNT_REF_NUMBER)
					)

					->setComment('Integration OMS Refund')
					->setOption('type', 'InnoDB')
					->setOption('charset', 'utf8');
				$installer->getConnection()->createTable($table);
			}
		}

		if (version_compare($context->getVersion(), '1.1.3', '<')) {
			$installer->getConnection()->addColumn(
				$installer->getTable('integration_oms_oar'),
				'code_name',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => false,
					'comment' => 'code_name',
					'after' => 'store_code',
				]
			);

			$installer->getConnection()->addColumn(
				$installer->getTable('integration_oms_oar'),
				'is_spo',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
					'nullable' => false,
					'comment' => 'is_spo',
					'after' => 'code_name',
				]
			);

			$installer->getConnection()->addColumn(
				$installer->getTable('integration_oms_oar'),
				'is_own_courier',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
					'nullable' => false,
					'comment' => 'is_fresh',
					'after' => 'is_spo',
				]
			);

			$installer->getConnection()->addColumn(
				$installer->getTable('integration_oms_oar'),
				'warehouse_source',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => false,
					'comment' => 'warehouse_source',
					'after' => 'is_own_courier',
				]
			);

			$installer->getConnection()->addColumn(
				$installer->getTable('integration_oms_oar'),
				'spo_detail',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => false,
					'comment' => 'spo_detail',
					'after' => 'warehouse_source',
				]
			);

			$installer->getConnection()->addColumn(
				$installer->getTable('integration_oms_oar'),
				'oar_origin_order_id',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => false,
					'comment' => 'oar_origin_order_id',
					'after' => 'oar_order_id',
				]
			);

			$setup->getConnection()->dropColumn($setup->getTable('integration_oms_oar'), 'is_warehouse');
		}

		if (version_compare($context->getVersion(), '1.1.4', '<')) {
			// Get table
			$tableName = $installer->getTable(IntegrationOrderReturnInterface::TABLE_NAME);
			// Check if the table already exists
			if ($installer->getConnection()->isTableExists($tableName) != true) {
				// Create table
				$table = $installer->getConnection()
					->newTable($tableName)
					->addColumn(
						IntegrationOrderReturnInterface::ID,
						Table::TYPE_INTEGER,
						null,
						[
							'identity' => true,
							'unsigned' => true,
							'nullable' => false,
							'primary' => true,
						],
						ucfirst(IntegrationOrderReturnInterface::ID)
					)
					->addColumn(
						IntegrationOrderReturnInterface::ORDER_ID,
						Table::TYPE_TEXT,
						null,
						['nullable' => true],
						ucfirst(IntegrationOrderReturnInterface::ORDER_ID)
					)
					->addColumn(
						IntegrationOrderReturnInterface::SKU,
						Table::TYPE_TEXT,
						null,
						['nullable' => true],
						ucfirst(IntegrationOrderReturnInterface::SKU)
					)
					->addColumn(
						IntegrationOrderReturnInterface::STORE,
						Table::TYPE_TEXT,
						null,
						['nullable' => true],
						ucfirst(IntegrationOrderReturnInterface::STORE)
					)
					->addColumn(
						IntegrationOrderReturnInterface::QTY_INITIATED,
						Table::TYPE_TEXT,
						null,
						['nullable' => true],
						ucfirst(IntegrationOrderReturnInterface::QTY_INITIATED)
					)
					->addColumn(
						IntegrationOrderReturnInterface::QTY_INPROGRESS,
						Table::TYPE_TEXT,
						null,
						['nullable' => true],
						ucfirst(IntegrationOrderReturnInterface::QTY_INPROGRESS)
					)
					->addColumn(
						IntegrationOrderReturnInterface::QTY_APPROVED,
						Table::TYPE_TEXT,
						null,
						['nullable' => true],
						ucfirst(IntegrationOrderReturnInterface::QTY_APPROVED)
					)
					->addColumn(
						IntegrationOrderReturnInterface::QTY_REJECTED,
						Table::TYPE_TEXT,
						null,
						['nullable' => true],
						ucfirst(IntegrationOrderReturnInterface::QTY_REJECTED)
					)
					->addColumn(
						IntegrationOrderReturnInterface::RETURN_REASON,
						Table::TYPE_TEXT,
						null,
						['nullable' => true],
						ucfirst(IntegrationOrderReturnInterface::RETURN_REASON)
					)
					->addColumn(
						IntegrationOrderReturnInterface::ITEM_CONDITION,
						Table::TYPE_TEXT,
						null,
						['nullable' => true],
						ucfirst(IntegrationOrderReturnInterface::ITEM_CONDITION)
					)
					->addColumn(
						IntegrationOrderReturnInterface::RESOLUTION,
						Table::TYPE_TEXT,
						null,
						['nullable' => true],
						ucfirst(IntegrationOrderReturnInterface::RESOLUTION)
					)
					->addColumn(
						IntegrationOrderReturnInterface::STATUS,
						Table::TYPE_TEXT,
						null,
						['nullable' => true],
						ucfirst(IntegrationOrderReturnInterface::STATUS)
					)
					->addColumn(
						IntegrationOrderReturnInterface::CREATED_AT,
						Table::TYPE_TIMESTAMP,
						null,
						['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
						'Created Time'
					)
					->addColumn(
						IntegrationOrderReturnInterface::UPDATED_AT,
						Table::TYPE_TIMESTAMP,
						null,
						['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
						'Updated Time'
					)
					->setComment('Integration Order return')
					->setOption('type', 'InnoDB')
					->setOption('charset', 'utf8');
				$installer->getConnection()->createTable($table);
			}
		}

		if (version_compare($context->getVersion(), '1.1.5', '<')) {
			$installer->getConnection()->addColumn(
				$installer->getTable('integration_oms_return'),
				'reference_number',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => false,
					'comment' => 'reference_number',
					'after' => 'order_id',
				]
			);

		}

		if (version_compare($context->getVersion(), '1.1.9', '<')) {
			$installer->getConnection()->addColumn(
				$installer->getTable('integration_oms_order_item'),
				'is_fresh',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
					'nullable' => true,
					'comment' => 'is_fresh for oms',
					'after' => 'is_warehouse',
				]
			);
		}
	}
}
