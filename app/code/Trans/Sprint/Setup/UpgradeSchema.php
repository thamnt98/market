<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Sprint\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Trans\Sprint\Api\Data\BankInterface;
use Trans\Sprint\Api\Data\SprintPaymentFlagInterface;
use Trans\Sprint\Api\Data\SprintRefundInterface;
use Trans\Sprint\Api\Data\SprintResponseInterface;
use Trans\Sprint\Api\Data\SprintCustomerTokenizationInterface;

/**
 * @SuppressWarnings(PHPMD)
 */
class UpgradeSchema implements UpgradeSchemaInterface {
	public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {
		$setup->startSetup();

		if (version_compare($context->getVersion(), '1.0.1', '<')) {
			$setup->getConnection()->addColumn(
				$setup->getTable(SprintResponseInterface::TABLE_NAME),
				SprintResponseInterface::PAYMENT_METHOD,
				[
					'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,
					'LENGTH'   => 50,
					'comment'  => 'Payment Method',
				]
			);
		}

		if (version_compare($context->getVersion(), '1.0.2', '<')) {
			$setup->getConnection()->addColumn(
				$setup->getTable(SprintResponseInterface::TABLE_NAME),
				SprintResponseInterface::FLAG,
				[
					'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,
					'LENGTH'   => 50,
					'comment'  => 'Payment Status',
				]
			);
		}

		if (version_compare($context->getVersion(), '1.0.5', '<')) {
			$setup->getConnection()->addColumn(
				$setup->getTable(SprintResponseInterface::TABLE_NAME),
				SprintResponseInterface::INSERT_DATE,
				[
					'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,
					'LENGTH'   => 50,
					'comment'  => 'Insert transaction date',
				]
			);

			$setup->getConnection()->addColumn(
				$setup->getTable(SprintResponseInterface::TABLE_NAME),
				SprintResponseInterface::EXPIRE_DATE,
				[
					'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,
					'LENGTH'   => 50,
					'comment'  => 'Expire transaction date',
				]
			);
		}

		if (version_compare($context->getVersion(), '1.0.6', '<')) {
			$this->installTablePaymentFlag($setup);
		}

		if (version_compare($context->getVersion(), '1.0.7', '<')) {
			$setup->getConnection()->addColumn(
				$setup->getTable(SprintResponseInterface::TABLE_NAME),
				SprintResponseInterface::CHANNEL_ID,
				[
					'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,
					'LENGTH'   => 50,
					'comment'  => 'Sprint Channel Id',
				]
			);
		}

		if (version_compare($context->getVersion(), '1.0.8', '<')) {
			$setup->getConnection()->addColumn(
				$setup->getTable(SprintResponseInterface::TABLE_NAME),
				SprintResponseInterface::CUSTOMER_ACCOUNT,
				[
					'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,
					'LENGTH'   => 50,
					'comment'  => 'Sprint Customer Account',
				]
			);
		}
		if (version_compare($context->getVersion(), '1.1.0', '<')) {
			$this->installTableBank($setup);
			$this->installTableBankBin($setup);
		}
		if (version_compare($context->getVersion(), '1.2.0', '<')) {
			$this->installTableRefund($setup);
		}

		if (version_compare($context->getVersion(), '1.2.1', '<')) {
			$this->updateTableQuoteOrder($setup);
		}

		if (version_compare($context->getVersion(), '1.2.2', '<')) {
			$this->installTableTokenization($setup);
		}

		if (version_compare($context->getVersion(), '1.2.3', '<')) {
			$this->addCardNoTablePaymentFlag($setup);
		}

		if (version_compare($context->getVersion(), '1.2.4', '<')) {
			$this->changeTransFeaTablePaymentFlag($setup);
		}

		$setup->endSetup();
	}

    /**
     * update table quote & sales order
     * @param SchemaSetupInterface $setup
     */
    protected function updateTableQuoteOrder($setup)
    {
        $quote = 'quote';
        $orderTable = 'sales_order';
        $serviceFee = 'service_fee';

        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quote),
                $serviceFee,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
                    'length' => '15,4',
                    'comment' => $serviceFee
                ]
            );
        
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($orderTable),
                $serviceFee,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
                    'length' => '15,4',
                    'comment' => $serviceFee
                ]
            );
    }

	/**
	 * Install Table Payment Flag
	 *  @param SchemaSetupInterface $setup
	 */
	protected function installTablePaymentFlag($setup) {
		$table = $setup->getConnection()
			->newTable($setup->getTable(SprintPaymentFlagInterface::TABLE_NAME))
			->addColumn(
				SprintPaymentFlagInterface::ID,
				Table::TYPE_INTEGER,
				null,
				['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
				'Id'
			)
			->addColumn(
				SprintPaymentFlagInterface::CURRENCY,
				Table::TYPE_TEXT,
				5,
				['nullable' => true],
				'Currency'
			)
			->addColumn(
				SprintPaymentFlagInterface::TRANSACTION_NO,
				Table::TYPE_TEXT,
				50,
				['nullable' => false],
				'Order Increment Id'
			)
			->addColumn(
				SprintPaymentFlagInterface::TRANSACTION_AMOUNT,
				Table::TYPE_TEXT,
				10,
				['nullable' => false],
				'Transaction amounr'
			)
			->addColumn(
				SprintPaymentFlagInterface::TRANSACTION_DATE,
				Table::TYPE_TEXT,
				30,
				['nullable' => true],
				'Transaction date'
			)
			->addColumn(
				SprintPaymentFlagInterface::CHANNEL_TYPE,
				Table::TYPE_TEXT,
				10,
				['nullable' => true],
				'Channel Type'
			)
			->addColumn(
				SprintPaymentFlagInterface::TRANSACTION_FEATURE,
				Table::TYPE_TEXT,
				200,
				['nullable' => true],
				'transaction feature'
			)
			->addColumn(
				SprintPaymentFlagInterface::TRANSACTION_STATUS,
				Table::TYPE_TEXT,
				5,
				['nullable' => true],
				'transaction status'
			)
			->addColumn(
				SprintPaymentFlagInterface::TRANSACTION_MESSAGE,
				Table::TYPE_TEXT,
				200,
				['nullable' => true],
				'transaction message'
			)
			->addColumn(
				SprintPaymentFlagInterface::CUSTOMER_ACCOUNT,
				Table::TYPE_TEXT,
				100,
				['nullable' => true],
				'customer account'
			)
			->addColumn(
				SprintPaymentFlagInterface::CARD_TOKEN,
				Table::TYPE_TEXT,
				30,
				['nullable' => true],
				'card token'
			)
			->addColumn(
				SprintPaymentFlagInterface::CARD_TOKEN_USE,
				Table::TYPE_TEXT,
				30,
				['nullable' => true],
				'card token use'
			)
			->addColumn(
				SprintPaymentFlagInterface::FLAG_TYPE,
				Table::TYPE_TEXT,
				5,
				['nullable' => true],
				'flag type'
			)
			->addColumn(
				SprintPaymentFlagInterface::INSERT_ID,
				Table::TYPE_TEXT,
				115,
				['nullable' => true],
				'insert id'
			)
			->addColumn(
				SprintPaymentFlagInterface::PAYMENT_REFF_ID,
				Table::TYPE_TEXT,
				30,
				['nullable' => true],
				'payment reff id'
			)
			->addColumn(
				SprintPaymentFlagInterface::AUTH_CODE,
				Table::TYPE_TEXT,
				100,
				['nullable' => true],
				'auth code'
			)
			->addColumn(
				SprintPaymentFlagInterface::ADDITIONAL_DATA,
				Table::TYPE_TEXT,
				225,
				['nullable' => true],
				'Additional Data'
			)->addColumn(
			SprintPaymentFlagInterface::CREATED_AT,
			\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
			null,
			['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
			'Created Time'
		)->addColumn(
			SprintPaymentFlagInterface::UPDATED_AT,
			\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
			null,
			['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
			'Updated Time'
		)
			->setComment('Payment Flag Table');

		$setup->getConnection()->createTable($table);
	}

	/**
	 * Install Table entity Bank
	 * @param SchemaSetupInterface $setup
	 *
	 */
	protected function installTableBank($setup) {
		$table = $setup->getConnection()
			->newTable($setup->getTable(BankInterface::TABLE_NAME))
			->addColumn(
				BankInterface::ID,
				Table::TYPE_INTEGER,
				null,
				['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
				'Id'
			)
			->addColumn(
				BankInterface::NAME,
				Table::TYPE_TEXT,
				225,
				['nullable' => false],
				'Bank Name'
			)
			->addColumn(
				BankInterface::CODE,
				Table::TYPE_TEXT,
				50,
				['nullable' => true],
				'Bank Code'
			)
			->addColumn(
				BankInterface::LABEL,
				Table::TYPE_TEXT,
				225,
				['nullable' => true],
				'Bank Label'
			)
			->addColumn(
				BankInterface::CREATED_AT,
				\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
				null,
				['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
				'Created Time'
			)
			->addColumn(
				BankInterface::UPDATED_AT,
				\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
				null,
				['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
				'Updated Time'
			)
			->setComment('Bank Table');

		$setup->getConnection()->createTable($table);
	}

	/**
	 * Install Bank Bin
	 * @param SchemaSetupInterface $setup
	 *
	 */
	protected function installTableBankBin($setup) {
		$table = $setup->getConnection()
			->newTable($setup->getTable(SprintPaymentFlagInterface::BANK_BIN_TABLE_NAME))
			->addColumn(
				SprintPaymentFlagInterface::ID,
				Table::TYPE_INTEGER,
				null,
				['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
				'Id'
			)
			->addColumn(
				SprintPaymentFlagInterface::BANK_ID,
				Table::TYPE_INTEGER,
				null,
				['nullable' => false, 'default' => 0],
				ucfirst(SprintPaymentFlagInterface::BANK_ID)
			)
			->addColumn(
				SprintPaymentFlagInterface::BIN_TYPE_ID,
				Table::TYPE_SMALLINT,
				null,
				['nullable' => false, 'default' => 1],
				'BIN TYPE : ' . SprintPaymentFlagInterface::BIN_TYPE_CC . ' = CREDIT CARD , ' . SprintPaymentFlagInterface::BIN_TYPE_DB . ' = DEBIT CARD'
			)
			->addColumn(
				SprintPaymentFlagInterface::BIN_CODE,
				Table::TYPE_TEXT,
				50,
				['nullable' => true],
				'BIN CODE'
			)
			->addColumn(
				SprintPaymentFlagInterface::CREATED_AT,
				\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
				null,
				['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
				'Created Time'
			)
			->addColumn(
				SprintPaymentFlagInterface::UPDATED_AT,
				\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
				null,
				['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
				'Updated Time'
			)
			->setComment('Bank Table');

		$setup->getConnection()->createTable($table);
	}

	/**
	 * Install Sprint Refund
	 * @param SchemaSetupInterface $setup
	 *
	 */
	protected function installTableRefund($setup) {
		$table = $setup->getConnection()
			->newTable($setup->getTable(SprintRefundInterface::TABLE_NAME))
			->addColumn(
				SprintRefundInterface::ID,
				Table::TYPE_INTEGER,
				null,
				['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
				'Id'
			)
			->addColumn(
				SprintRefundInterface::CHANNEL_ID,
				Table::TYPE_TEXT,
				24,
				['nullable' => false],
				ucfirst(SprintRefundInterface::CHANNEL_ID)
			)
			->addColumn(
				SprintRefundInterface::TRANSACTION_NO,
				Table::TYPE_TEXT,
				18,
				['nullable' => false],
				ucfirst(SprintRefundInterface::TRANSACTION_NO)
			)
			->addColumn(
				SprintRefundInterface::TRANSACTION_AMOUNT,
				Table::TYPE_DECIMAL,
				20.4,
				['nullable' => false],
				ucfirst(SprintRefundInterface::TRANSACTION_AMOUNT)
			)
			->addColumn(
				SprintRefundInterface::ACQUIRER_APPROVAL_CODE,
				Table::TYPE_TEXT,
				10,
				['nullable' => false],
				ucfirst(SprintRefundInterface::ACQUIRER_APPROVAL_CODE)
			)
			->addColumn(
				SprintRefundInterface::ACQUIRER_RESPONSE_CODE,
				Table::TYPE_TEXT,
				10,
				['nullable' => false],
				ucfirst(SprintRefundInterface::ACQUIRER_RESPONSE_CODE)
			)
			->addColumn(
				SprintRefundInterface::TRANSACTION_STATUS,
				Table::TYPE_TEXT,
				2,
				['nullable' => false],
				ucfirst(SprintRefundInterface::TRANSACTION_STATUS)
			)
			->addColumn(
				SprintRefundInterface::TRANSACTION_MESSAGE,
				Table::TYPE_TEXT,
				50,
				['nullable' => false],
				ucfirst(SprintRefundInterface::TRANSACTION_MESSAGE)
			)
			->addColumn(
				SprintRefundInterface::TRANSACTION_TYPE,
				Table::TYPE_TEXT,
				50,
				['nullable' => false],
				ucfirst(SprintRefundInterface::TRANSACTION_TYPE)
			)
			->addColumn(
				SprintRefundInterface::TRANSACTION_REFF_ID,
				Table::TYPE_TEXT,
				24,
				['nullable' => false],
				ucfirst(SprintRefundInterface::TRANSACTION_REFF_ID)
			)
			->addColumn(
				SprintRefundInterface::CREATED_AT,
				\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
				null,
				['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
				'Created Time'
			)
			->addColumn(
				SprintRefundInterface::UPDATED_AT,
				\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
				null,
				['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
				'Updated Time'
			)
			->setComment('Sprint Refund');

		$setup->getConnection()->createTable($table);
	}

	/**
	 * create table sprint_customer_tokenization
	 * @param $setup
	 */
	protected function installTableTokenization($setup)
	{
		$table = $setup->getConnection()
			->newTable($setup->getTable(SprintCustomerTokenizationInterface::TABLE_NAME))
			->addColumn(
				SprintCustomerTokenizationInterface::ID,
				Table::TYPE_INTEGER,
				null,
				['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
				'Id'
			)
			->addColumn(
				SprintCustomerTokenizationInterface::CUSTOMER_ID,
				Table::TYPE_INTEGER,
				null,
				['nullable' => false, 'default' => 0],
				'Customer ID'
			)
			->addColumn(
				SprintCustomerTokenizationInterface::MASKED_CARD_NO,
				Table::TYPE_TEXT,
				50,
				['nullable' => false, 'default' => 1],
				'Masking Credit Card Number'
			)
			->addColumn(
				SprintCustomerTokenizationInterface::CARD_TOKEN,
				Table::TYPE_TEXT,
				50,
				['nullable' => true],
				'Sprint Credit Card Token'
			)
			->addColumn(
				SprintCustomerTokenizationInterface::CREATED_AT,
				\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
				null,
				['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
				'Created Time'
			)
			->addColumn(
				SprintCustomerTokenizationInterface::UPDATED_AT,
				\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
				null,
				['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
				'Updated Time'
			)
			->setComment('Sprint Customer Tokenization');

		$setup->getConnection()->createTable($table);
	}

	/**
	 * add cardNo table payment
	 *  @param SchemaSetupInterface $setup
	 */
	protected function addCardNoTablePaymentFlag($setup) {
		$setup->getConnection()
            ->addColumn(
                $setup->getTable(SprintPaymentFlagInterface::TABLE_NAME),
                SprintPaymentFlagInterface::CARD_NO,
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => '30',
                    'after' => SprintPaymentFlagInterface::CARD_TOKEN_USE,
                    'comment' => 'Card No'
                ]
            );
	}

	/**
	 * change type transaction feature table payment
	 *  @param SchemaSetupInterface $setup
	 */
	protected function changeTransFeaTablePaymentFlag($setup) {
		$setup->getConnection()->changeColumn(
            $setup->getTable(SprintPaymentFlagInterface::TABLE_NAME),
            SprintPaymentFlagInterface::TRANSACTION_FEATURE,
            SprintPaymentFlagInterface::TRANSACTION_FEATURE,
            [
            	'type' => Table::TYPE_TEXT,
                'length' => null
            ]
        );
	}
}