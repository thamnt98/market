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

use \Magento\Framework\Db\Ddl\Table;
use \Magento\Framework\Setup\InstallSchemaInterface;
use \Magento\Framework\Setup\ModuleContextInterface;
use \Magento\Framework\Setup\SchemaSetupInterface;
use \Trans\IntegrationOrder\Api\Data\IntegrationOrderAllocationRuleInterface;
use \Trans\IntegrationOrder\Api\Data\IntegrationOrderHistoryInterface;
use \Trans\IntegrationOrder\Api\Data\IntegrationOrderInterface;
use \Trans\IntegrationOrder\Api\Data\IntegrationOrderItemInterface;
use \Trans\IntegrationOrder\Api\Data\IntegrationOrderLogInterface;
use \Trans\IntegrationOrder\Api\Data\IntegrationOrderPaymentInterface;
use \Trans\IntegrationOrder\Api\Data\IntegrationOrderStatusInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {

        $setup->startSetup();

        $this->integrationOrder($setup);
        $this->integrationOrderItem($setup);
        $this->integrationOrderPayment($setup);
        $this->integrationOrderLog($setup);
        $this->integrationOrderAllocationRule($setup);
        $this->integrationOrderStatus($setup);
        $this->integrationOrderHistory($setup);
        $setup->endSetup();
    }

    /**
     * Create Table OMS Order Mapping
     * @param $installer
     */
    public function integrationOrder($setup)
    {

        // Get table
        $tableName = $setup->getTable(IntegrationOrderInterface::TABLE_NAME);
        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            // Create table
            $table = $setup->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    IntegrationOrderInterface::OMS_ID_ORDER,
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true,
                    ],
                    ucfirst(IntegrationOrderInterface::OMS_ID_ORDER)
                )
                ->addColumn(
                    IntegrationOrderInterface::REFERENCE_NUMBER,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => null],
                    ucfirst(IntegrationOrderInterface::REFERENCE_NUMBER)
                )
                ->addColumn(
                    IntegrationOrderInterface::ORDER_ID,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderInterface::ORDER_ID)
                )
                ->addColumn(
                    IntegrationOrderInterface::ORDER_STATUS,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderInterface::ORDER_STATUS)
                )
                ->addColumn(
                    IntegrationOrderInterface::ORDER_TYPE,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderInterface::ORDER_TYPE)
                )
                ->addColumn(
                    IntegrationOrderInterface::ORDER_SOURCE,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => null],
                    ucfirst(IntegrationOrderInterface::ORDER_SOURCE)
                )
                ->addColumn(
                    IntegrationOrderInterface::SOURCE_STORE,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderInterface::SOURCE_STORE)
                )
                ->addColumn(
                    IntegrationOrderInterface::FULFILLMENT_STORE,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => null],
                    ucfirst(IntegrationOrderInterface::FULFILLMENT_STORE)
                )
                ->addColumn(
                    IntegrationOrderInterface::TENDER_TYPE,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => null],
                    ucfirst(IntegrationOrderInterface::TENDER_TYPE)
                )
                ->addColumn(
                    IntegrationOrderInterface::COURIER,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderInterface::COURIER)
                )
                ->addColumn(
                    IntegrationOrderInterface::SHIPMENT_TYPE,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderInterface::SHIPMENT_TYPE)
                )
                ->addColumn(
                    IntegrationOrderInterface::SHIPMENT_DATE,
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                    ucfirst(IntegrationOrderInterface::SHIPMENT_DATE)
                )
                ->addColumn(
                    IntegrationOrderInterface::MERCHANT_ID,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderInterface::MERCHANT_ID)
                )
                ->addColumn(
                    IntegrationOrderInterface::ORDER_CREATED_DATE,
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => true, 'default' => Table::TIMESTAMP_INIT],
                    ucfirst(IntegrationOrderInterface::ORDER_CREATED_DATE)
                )
                ->addColumn(
                    IntegrationOrderInterface::ACCOUNT_NAME,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ''],
                    ucfirst(IntegrationOrderInterface::ACCOUNT_NAME)
                )
                ->addColumn(
                    IntegrationOrderInterface::ACCOUNT_PHONE_NUMBER,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ''],
                    ucfirst(IntegrationOrderInterface::ACCOUNT_PHONE_NUMBER)
                )
                ->addColumn(
                    IntegrationOrderInterface::RECIPIENT_EMAIL_ADDRESS,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ''],
                    ucfirst(IntegrationOrderInterface::RECIPIENT_EMAIL_ADDRESS)
                )
                ->addColumn(
                    IntegrationOrderInterface::SHIPPING_ADDRESS,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderInterface::SHIPPING_ADDRESS)
                )
                ->addColumn(
                    IntegrationOrderInterface::BILLING_ADDRESS,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderInterface::BILLING_ADDRESS)
                )
                ->addColumn(
                    IntegrationOrderInterface::PROVINCE,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ''],
                    ucfirst(IntegrationOrderInterface::PROVINCE)
                )
                ->addColumn(
                    IntegrationOrderInterface::CITY,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderInterface::CITY)
                )
                ->addColumn(
                    IntegrationOrderInterface::DISTRICT,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderInterface::DISTRICT)
                )
                ->addColumn(
                    IntegrationOrderInterface::ZIPCODE,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ''],
                    ucfirst(IntegrationOrderInterface::ZIPCODE)
                )
                ->addColumn(
                    IntegrationOrderInterface::LONGITUDE,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderInterface::LONGITUDE)
                )
                ->addColumn(
                    IntegrationOrderInterface::LATITUDE,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderInterface::LATITUDE)
                )

                ->setComment('Integration OMS Order')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $setup->getConnection()->createTable($table);
        }
    }

    /**
     * Create Table OMS Order Item Mapping
     * @param $installer
     */
    public function integrationOrderItem($setup)
    {

        // Get table
        $tableName = $setup->getTable(IntegrationOrderItemInterface::TABLE_NAME);
        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            // Create table
            $table = $setup->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    IntegrationOrderItemInterface::OMS_ID_ORDER_ITEM,
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true,
                    ],
                    ucfirst(IntegrationOrderItemInterface::OMS_ID_ORDER_ITEM)
                )
                ->addColumn(
                    IntegrationOrderItemInterface::ORDER_ID,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => null],
                    ucfirst(IntegrationOrderItemInterface::ORDER_ID)
                )
                ->addColumn(
                    IntegrationOrderItemInterface::SALES_ORDER_ITEM_ID,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderItemInterface::SALES_ORDER_ITEM_ID)
                )
                ->addColumn(
                    IntegrationOrderItemInterface::PRODUCT_NAME,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderItemInterface::PRODUCT_NAME)
                )
                ->addColumn(
                    IntegrationOrderItemInterface::PRODUCT_WEIGHT,
                    Table::TYPE_FLOAT,
                    null,
                    ['nullable' => false, 'default' => 5, 5],
                    ucfirst(IntegrationOrderItemInterface::PRODUCT_WEIGHT)
                )
                ->addColumn(
                    IntegrationOrderItemInterface::PRODUCT_HEIGHT,
                    Table::TYPE_FLOAT,
                    null,
                    ['nullable' => false, 'default' => 5, 5],
                    ucfirst(IntegrationOrderItemInterface::PRODUCT_HEIGHT)
                )
                ->addColumn(
                    IntegrationOrderItemInterface::PRODUCT_WIDTH,
                    Table::TYPE_FLOAT,
                    null,
                    ['nullable' => false, 'default' => 5, 5],
                    ucfirst(IntegrationOrderItemInterface::PRODUCT_WIDTH)
                )
                ->addColumn(
                    IntegrationOrderItemInterface::PRODUCT_LENGTH,
                    Table::TYPE_FLOAT,
                    null,
                    ['nullable' => false, 'default' => 5, 5],
                    ucfirst(IntegrationOrderItemInterface::PRODUCT_LENGTH)
                )
                ->addColumn(
                    IntegrationOrderItemInterface::PRODUCT_SIZE,
                    Table::TYPE_FLOAT,
                    null,
                    ['nullable' => false, 'default' => 3, 3],
                    ucfirst(IntegrationOrderItemInterface::PRODUCT_SIZE)
                )
                ->addColumn(
                    IntegrationOrderItemInterface::PRODUCT_DIAMETER,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderItemInterface::PRODUCT_DIAMETER)
                )
                ->addColumn(
                    IntegrationOrderItemInterface::SKU,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderItemInterface::SKU)
                )
                ->addColumn(
                    IntegrationOrderItemInterface::ORIGINAL_PRICE,
                    Table::TYPE_FLOAT,
                    null,
                    ['nullable' => false, 'default' => 5, 5],
                    ucfirst(IntegrationOrderItemInterface::ORIGINAL_PRICE)
                )
                ->addColumn(
                    IntegrationOrderItemInterface::SELLING_PRICE,
                    Table::TYPE_FLOAT,
                    null,
                    ['nullable' => false, 'default' => 5, 5],
                    ucfirst(IntegrationOrderItemInterface::SELLING_PRICE)
                )
                ->addColumn(
                    IntegrationOrderItemInterface::DISCOUNT_AMOUNT,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderItemInterface::DISCOUNT_AMOUNT)
                )
                ->addColumn(
                    IntegrationOrderItemInterface::QTY,
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'default' => 0],
                    ucfirst(IntegrationOrderItemInterface::QTY)
                )
                ->addColumn(
                    IntegrationOrderItemInterface::TOTAL_WEIGHT,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderItemInterface::TOTAL_WEIGHT)
                )
                ->addColumn(
                    IntegrationOrderItemInterface::SUBTOTAL,
                    Table::TYPE_FLOAT,
                    null,
                    ['nullable' => false, 'default' => 5, 5],
                    ucfirst(IntegrationOrderItemInterface::SUBTOTAL)
                )
                ->addColumn(
                    IntegrationOrderItemInterface::VOUCHER_CODE,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderItemInterface::VOUCHER_CODE)
                )
                ->addColumn(
                    IntegrationOrderItemInterface::PROMO_ID,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderItemInterface::PROMO_ID)
                )
                ->addColumn(
                    IntegrationOrderItemInterface::FINAL_TOTAL,
                    Table::TYPE_FLOAT,
                    null,
                    ['nullable' => false, 'default' => 5, 5],
                    ucfirst(IntegrationOrderItemInterface::FINAL_TOTAL)
                )
                ->addColumn(
                    IntegrationOrderItemInterface::SUBTOTAL_ORDER,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderItemInterface::SUBTOTAL_ORDER)
                )
                ->addColumn(
                    IntegrationOrderItemInterface::SHIPPING_FEE,
                    Table::TYPE_FLOAT,
                    null,
                    ['nullable' => false, 'default' => 5, 5],
                    ucfirst(IntegrationOrderItemInterface::SHIPPING_FEE)
                )
                ->addColumn(
                    IntegrationOrderItemInterface::TOTAL_DISCOUNT_AMOUNT,
                    Table::TYPE_FLOAT,
                    null,
                    ['nullable' => false, 'default' => 5, 5],
                    ucfirst(IntegrationOrderItemInterface::TOTAL_DISCOUNT_AMOUNT)
                )
                ->addColumn(
                    IntegrationOrderItemInterface::GRAND_TOTAL,
                    Table::TYPE_FLOAT,
                    null,
                    ['nullable' => false, 'default' => 5, 5],
                    ucfirst(IntegrationOrderItemInterface::GRAND_TOTAL)
                )

                ->setComment('Integration OMS Item Order')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $setup->getConnection()->createTable($table);
        }
    }

    /**
     * Create Table OMS Order Payment Mapping
     * @param $installer
     */
    public function integrationOrderPayment($setup)
    {

        // Get table
        $tableName = $setup->getTable(IntegrationOrderPaymentInterface::TABLE_NAME);
        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            // Create table
            $table = $setup->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    IntegrationOrderPaymentInterface::OMS_ID_ORDER_PAYMENT,
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true,
                    ],
                    ucfirst(IntegrationOrderPaymentInterface::OMS_ID_ORDER_PAYMENT)
                )
                ->addColumn(
                    IntegrationOrderPaymentInterface::REFERENCE_NUMBER,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => null],
                    ucfirst(IntegrationOrderPaymentInterface::REFERENCE_NUMBER)
                )
                ->addColumn(
                    IntegrationOrderPaymentInterface::PAYMENT_REF_NUMBER_1,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderPaymentInterface::PAYMENT_REF_NUMBER_1)
                )
                ->addColumn(
                    IntegrationOrderPaymentInterface::PAYMENT_REF_NUMBER_2,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderPaymentInterface::PAYMENT_REF_NUMBER_2)
                )
                ->addColumn(
                    IntegrationOrderPaymentInterface::ORDER_PAID_DATE_TIME,
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                    ucfirst(IntegrationOrderPaymentInterface::ORDER_PAID_DATE_TIME)
                )
                ->addColumn(
                    IntegrationOrderPaymentInterface::CREATE_ORDER_DATE_TIME,
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                    ucfirst(IntegrationOrderPaymentInterface::CREATE_ORDER_DATE_TIME)
                )
                ->addColumn(
                    IntegrationOrderPaymentInterface::SPLIT_PAYMENT,
                    Table::TYPE_BOOLEAN,
                    null,
                    ['nullable' => false, 'default' => 0],
                    ucfirst(IntegrationOrderPaymentInterface::SPLIT_PAYMENT)
                )
                ->addColumn(
                    IntegrationOrderPaymentInterface::PAYMENT_TYPE_1,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderPaymentInterface::PAYMENT_TYPE_1)
                )
                ->addColumn(
                    IntegrationOrderPaymentInterface::PAYMENT_TYPE_2,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderPaymentInterface::PAYMENT_TYPE_2)
                )
                ->addColumn(
                    IntegrationOrderPaymentInterface::AMOUNT_OF_PAYMENT_1,
                    Table::TYPE_FLOAT,
                    null,
                    ['nullable' => false, 'default' => 5, 5],
                    ucfirst(IntegrationOrderPaymentInterface::AMOUNT_OF_PAYMENT_1)
                )
                ->addColumn(
                    IntegrationOrderPaymentInterface::AMOUNT_OF_PAYMENT_2,
                    Table::TYPE_FLOAT,
                    null,
                    ['nullable' => false, 'default' => 5, 5],
                    ucfirst(IntegrationOrderPaymentInterface::AMOUNT_OF_PAYMENT_2)
                )
                ->addColumn(
                    IntegrationOrderPaymentInterface::TOTAL_AMOUNT_PAID,
                    Table::TYPE_FLOAT,
                    null,
                    ['nullable' => false, 'default' => 5, 5],
                    ucfirst(IntegrationOrderPaymentInterface::TOTAL_AMOUNT_PAID)
                )
                ->addColumn(
                    IntegrationOrderPaymentInterface::PAYMENT_STATUS,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderPaymentInterface::PAYMENT_STATUS)
                )

                ->setComment('Integration OMS Item Order')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $setup->getConnection()->createTable($table);
        }
    }

    /**
     * Create Table OMS Order Log Mapping
     * @param $installer
     */
    public function integrationOrderLog($setup)
    {

        // Get table
        $tableName = $setup->getTable(IntegrationOrderLogInterface::TABLE_NAME);
        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            // Create table
            $table = $setup->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    IntegrationOrderLogInterface::OMS_ID_ORDER_LOG,
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true,
                    ],
                    ucfirst(IntegrationOrderLogInterface::OMS_ID_ORDER_LOG)
                )
                ->addColumn(
                    IntegrationOrderLogInterface::REFERENCE_NUMBER,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => null],
                    ucfirst(IntegrationOrderLogInterface::REFERENCE_NUMBER)
                )
                ->addColumn(
                    IntegrationOrderLogInterface::PAYMENT_PENDING,
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderLogInterface::PAYMENT_PENDING)
                )
                ->addColumn(
                    IntegrationOrderLogInterface::IN_PROCESS,
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderLogInterface::IN_PROCESS)
                )
                ->addColumn(
                    IntegrationOrderLogInterface::READY_TO_DELIVER,
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderLogInterface::READY_TO_DELIVER)
                )
                ->addColumn(
                    IntegrationOrderLogInterface::READY_TO_PICKUP,
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderLogInterface::READY_TO_PICKUP)
                )
                ->addColumn(
                    IntegrationOrderLogInterface::OUT_OF_DELIVERY,
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderLogInterface::OUT_OF_DELIVERY)
                )
                ->addColumn(
                    IntegrationOrderLogInterface::IN_TRANSIT,
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderLogInterface::IN_TRANSIT)
                )
                ->addColumn(
                    IntegrationOrderLogInterface::GOODS_ACCEPTANCE,
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderLogInterface::GOODS_ACCEPTANCE)
                )

                ->setComment('Integration OMS Log Order')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $setup->getConnection()->createTable($table);
        }
    }

    /**
     * Create Table Order Allocation Rule Mapping
     * @param $installer
     */
    public function integrationOrderAllocationRule($setup)
    {

        // Get table
        $tableName = $setup->getTable(IntegrationOrderAllocationRuleInterface::TABLE_NAME);
        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            // Create table
            $table = $setup->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    IntegrationOrderAllocationRuleInterface::OAR_IDS,
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true,
                    ],
                    ucfirst(IntegrationOrderAllocationRuleInterface::OAR_IDS)
                )
                ->addColumn(
                    IntegrationOrderAllocationRuleInterface::QUOTE_ID,
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'default' => 0],
                    ucfirst(IntegrationOrderAllocationRuleInterface::QUOTE_ID)
                )
                ->addColumn(
                    IntegrationOrderAllocationRuleInterface::ADDRESS_ID,
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'default' => 0],
                    ucfirst(IntegrationOrderAllocationRuleInterface::ADDRESS_ID)
                )
                ->addColumn(
                    IntegrationOrderAllocationRuleInterface::CUSTOMER_ID,
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'default' => 0],
                    ucfirst(IntegrationOrderAllocationRuleInterface::CUSTOMER_ID)
                )
                ->addColumn(
                    IntegrationOrderAllocationRuleInterface::OAR_CUSTOMER_ID,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderAllocationRuleInterface::OAR_CUSTOMER_ID)
                )
                ->addColumn(
                    IntegrationOrderAllocationRuleInterface::OAR_ORDER_ID,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderAllocationRuleInterface::OAR_ORDER_ID)
                )
                ->addColumn(
                    IntegrationOrderAllocationRuleInterface::STORE_CODE,
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'default' => 0],
                    ucfirst(IntegrationOrderAllocationRuleInterface::STORE_CODE)
                )
                ->addColumn(
                    IntegrationOrderAllocationRuleInterface::ORDER_ID,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderAllocationRuleInterface::ORDER_ID)
                )
                ->addColumn(
                    IntegrationOrderAllocationRuleInterface::REFERENCE_NUMBER,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    ucfirst(IntegrationOrderAllocationRuleInterface::REFERENCE_NUMBER)
                )

                ->setComment('Integration OAR')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $setup->getConnection()->createTable($table);
        }
    }

    /**
     * Create Table Order Status Mapping
     * @param $installer
     */
    public function integrationOrderStatus($setup)
    {

        // Get table
        $tableName = $setup->getTable(IntegrationOrderStatusInterface::TABLE_NAME);
        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            // Create table
            $table = $setup->getConnection()
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
            $setup->getConnection()->createTable($table);
        }
    }

    /**
     * Create Table Order History Mapping
     * @param $installer
     */
    public function integrationOrderHistory($setup)
    {
        // Get table
        $tableName = $setup->getTable(IntegrationOrderHistoryInterface::TABLE_NAME);
        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            // Create table
            $table = $setup->getConnection()
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
            $setup->getConnection()->createTable($table);
        }
    }
}
