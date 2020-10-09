<?php
/**
 * @category Trans
 * @package  Trans_DigitalProduct
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\DigitalProduct\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Trans\DigitalProduct\Api\Data\DigitalProductOperatorListInterface;

class InstallData implements InstallDataInterface {

	public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
		$setup->startSetup();

		$this->digitalProductOperatorList($setup);

		$setup->endSetup();
	}

	/**
	 * Insert data to table Operator List
	 * @param $installer
	 */
	public function digitalProductOperatorList($setup) {
		$tableName = $setup->getTable(DigitalProductOperatorListInterface::TABLE_NAME);
		if ($setup->getConnection()->isTableExists($tableName) == true) {
			$data = [
				["001", "XL Axiata", "XL", "0859"],
				["001", "XL Axiata", "XL", "0877"],
				["001", "XL Axiata", "XL", "0878"],
				["001", "XL Axiata", "XL", "0817"],
				["001", "XL Axiata", "XL", "0818"],
				["001", "XL Axiata", "XL", "0819"],
				["002", "XL Axiata", "Axis", "0832"],
				["002", "XL Axiata", "Axis", "0833"],
				["002", "XL Axiata", "Axis", "0838"],
				["002", "XL Axiata", "Axis", "0831"],
				["003", "Telkomsel", "Halo (Telkomsel Postpaid)", "0811"],
				["003", "Telkomsel", "Halo (Telkomsel Postpaid)", "0812"],
				["003", "Telkomsel", "Halo (Telkomsel Postpaid)", "0813"],
				["004", "Telkomsel", "Simpati", "0821"],
				["004", "Telkomsel", "Simpati", "0822"],
				["005", "Telkomsel", "Kartu As", "0823"],
				["005", "Telkomsel", "Kartu As", "0852"],
				["005", "Telkomsel", "Kartu As", "0853"],
				["005", "Telkomsel", "Kartu As", "0851"],
				["006", "Tri", "Tri", "0898"],
				["006", "Tri", "Tri", "0899"],
				["006", "Tri", "Tri", "0895"],
				["006", "Tri", "Tri", "0896"],
				["006", "Tri", "Tri", "0897"],
				["007", "Indosat", "Indosat M2 Broadband", "0814"],
				["008", "Indosat", "Matrix & Mentari", "0815"],
				["008", "Indosat", "Matrix & Mentari", "0816"],
				["008", "Indosat", "Matrix & Mentari", "0855"],
				["008", "Indosat", "Matrix & Mentari", "0858"],
				["009", "Indosat", "IM3", "0856"],
				["009", "Indosat", "IM3", "0857"],
				["010", "Smartfren", "Smartfren", "0881"],
				["010", "Smartfren", "Smartfren", "0882"],
				["010", "Smartfren", "Smartfren", "0883"],
				["010", "Smartfren", "Smartfren", "0884"],
				["010", "Smartfren", "Smartfren", "0885"],
				["010", "Smartfren", "Smartfren", "0886"],
				["010", "Smartfren", "Smartfren", "0887"],
				["010", "Smartfren", "Smartfren", "0888"],
				["010", "Smartfren", "Smartfren", "0889"],
			];
			$columns = [DigitalProductOperatorListInterface::BRAND_ID, DigitalProductOperatorListInterface::OPERATOR_NAME, DigitalProductOperatorListInterface::SERVICE_NAME, DigitalProductOperatorListInterface::PREFIX_NUMBER];
			$setup->getConnection()->insertArray($tableName, $columns, $data);
		}
	}
}