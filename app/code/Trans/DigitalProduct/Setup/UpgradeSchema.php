<?php
/**
 * @category Trans
 * @package  Trans_DigitalProduct
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 * @author   hadi <ashadi.sejati@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\DigitalProduct\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Trans\DigitalProduct\Api\Data\DigitalProductInquireResponseInterface;
use Trans\DigitalProduct\Api\Data\DigitalProductTransactionResponseInterface;
use Trans\DigitalProduct\Api\Data\DigitalProductTransactionStatusInterface;

/**
 * @SuppressWarnings(PHPMD)
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '0.0.2', '<')) {
            $this->installTableDigitalProductInquireResponse($setup);
            $this->installTableDigitalProductTransactionResponse($setup);
        }

        if (version_compare($context->getVersion(), '1.0.0', '<')) {
            $this->installTableDigitalProductTransactionStatus($setup);
        }

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $setup->getConnection()->changeColumn(
                $setup->getTable(DigitalProductInquireResponseInterface::TABLE_NAME),
                DigitalProductInquireResponseInterface::STATUS,
                DigitalProductInquireResponseInterface::STATUS,
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 25,
                    'nullable' => true,
                    'comment' => ucfirst(DigitalProductInquireResponseInterface::STATUS)
                ]
            );

            $setup->getConnection()->changeColumn(
                $setup->getTable(DigitalProductTransactionResponseInterface::TABLE_NAME),
                DigitalProductTransactionResponseInterface::STATUS,
                DigitalProductTransactionResponseInterface::STATUS,
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 25,
                    'nullable' => true,
                    'comment' => ucfirst(DigitalProductTransactionResponseInterface::STATUS)
                ]
            );

            $setup->getConnection()->changeColumn(
                $setup->getTable(DigitalProductTransactionStatusInterface::TABLE_NAME),
                DigitalProductTransactionStatusInterface::STATUS,
                DigitalProductTransactionStatusInterface::STATUS,
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 25,
                    'nullable' => true,
                    'comment' => ucfirst(DigitalProductTransactionStatusInterface::STATUS)
                ]
            );
        }

        $setup->endSetup();
    }

    /**
     * Install Sprint Refund
     * @param SchemaSetupInterface $setup
     *
     */
    protected function installTableDigitalProductInquireResponse($setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(DigitalProductInquireResponseInterface::TABLE_NAME))
            ->addColumn(
                DigitalProductInquireResponseInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                'Id'
            )
            ->addColumn(
                DigitalProductInquireResponseInterface::CUSTOMER_ID,
                Table::TYPE_TEXT,
                25,
                ['nullable' => false],
                ucfirst(DigitalProductInquireResponseInterface::CUSTOMER_ID)
            )
            ->addColumn(
                DigitalProductInquireResponseInterface::REQUEST,
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                ucfirst(DigitalProductInquireResponseInterface::REQUEST)
            )
            ->addColumn(
                DigitalProductInquireResponseInterface::RESPONSE,
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                ucfirst(DigitalProductInquireResponseInterface::RESPONSE)
            )
            ->addColumn(
                DigitalProductInquireResponseInterface::STATUS,
                Table::TYPE_TEXT,
                5,
                ['nullable' => true],
                ucfirst(DigitalProductInquireResponseInterface::STATUS)
            )
            ->addColumn(
                DigitalProductInquireResponseInterface::MESSAGE,
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                ucfirst(DigitalProductInquireResponseInterface::MESSAGE)
            )
            ->addColumn(
                DigitalProductInquireResponseInterface::CREATED_AT,
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created Time'
            )
            ->addColumn(
                DigitalProductInquireResponseInterface::UPDATED_AT,
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Updated Time'
            )
            ->setComment('Digital Product Inquire Response');

        $setup->getConnection()->createTable($table);
    }

    /**
     * Install Sprint Refund
     * @param SchemaSetupInterface $setup
     *
     */
    protected function installTableDigitalProductTransactionResponse($setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(DigitalProductTransactionResponseInterface::TABLE_NAME))
            ->addColumn(
                DigitalProductTransactionResponseInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                'Id'
            )
            ->addColumn(
                DigitalProductTransactionResponseInterface::CUSTOMER_ID,
                Table::TYPE_TEXT,
                25,
                ['nullable' => false],
                ucfirst(DigitalProductTransactionResponseInterface::CUSTOMER_ID)
            )
            ->addColumn(
                DigitalProductTransactionResponseInterface::ORDER_ID,
                Table::TYPE_TEXT,
                25,
                ['nullable' => false],
                ucfirst(DigitalProductTransactionResponseInterface::ORDER_ID)
            )
            ->addColumn(
                DigitalProductTransactionResponseInterface::REQUEST,
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                ucfirst(DigitalProductTransactionResponseInterface::REQUEST)
            )
            ->addColumn(
                DigitalProductTransactionResponseInterface::RESPONSE,
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                ucfirst(DigitalProductTransactionResponseInterface::RESPONSE)
            )
            ->addColumn(
                DigitalProductTransactionResponseInterface::STATUS,
                Table::TYPE_TEXT,
                5,
                ['nullable' => true],
                ucfirst(DigitalProductTransactionResponseInterface::STATUS)
            )
            ->addColumn(
                DigitalProductTransactionResponseInterface::MESSAGE,
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                ucfirst(DigitalProductTransactionResponseInterface::MESSAGE)
            )
            ->addColumn(
                DigitalProductTransactionResponseInterface::CREATED_AT,
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created Time'
            )
            ->addColumn(
                DigitalProductTransactionResponseInterface::UPDATED_AT,
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Updated Time'
            )
            ->setComment('Digital Product Transaction Response');

        $setup->getConnection()->createTable($table);
    }

    /**
     * Install digital product transaction status
     * @param SchemaSetupInterface $setup
     *
     */
    protected function installTableDigitalProductTransactionStatus($setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(DigitalProductTransactionStatusInterface::TABLE_NAME))
            ->addColumn(
                DigitalProductTransactionStatusInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                'Id'
            )
            ->addColumn(
                DigitalProductTransactionStatusInterface::CUSTOMER_ID,
                Table::TYPE_TEXT,
                25,
                ['nullable' => false],
                ucfirst(DigitalProductTransactionStatusInterface::CUSTOMER_ID)
            )
            ->addColumn(
                DigitalProductTransactionStatusInterface::ORDER_ID,
                Table::TYPE_TEXT,
                25,
                ['nullable' => false],
                ucfirst(DigitalProductTransactionStatusInterface::ORDER_ID)
            )
            ->addColumn(
                DigitalProductTransactionStatusInterface::RESPONSE,
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                ucfirst(DigitalProductTransactionStatusInterface::RESPONSE)
            )
            ->addColumn(
                DigitalProductTransactionStatusInterface::STATUS,
                Table::TYPE_TEXT,
                5,
                ['nullable' => true],
                ucfirst(DigitalProductTransactionStatusInterface::STATUS)
            )
            ->addColumn(
                DigitalProductTransactionStatusInterface::MESSAGE,
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                ucfirst(DigitalProductTransactionStatusInterface::MESSAGE)
            )
            ->addColumn(
                DigitalProductTransactionStatusInterface::CREATED_AT,
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created Time'
            )
            ->addColumn(
                DigitalProductTransactionStatusInterface::UPDATED_AT,
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Updated Time'
            )
            ->setComment('Digital Product Transaction Status');

        $setup->getConnection()->createTable($table);
    }
}
