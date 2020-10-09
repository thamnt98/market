<?php
/**
 * @category Trans
 * @package  Trans_IntegrationOrder
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.com>
 *
 * Copyright © 2020 PT CT CORP DIGITAL. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationOrder\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface {
	/**
	 * Installs DB schema for a module
	 *
	 * @param SchemaSetupInterface $setup
	 * @param ModuleContextInterface $context
	 * @return void
	 */
	public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
		$installer = $setup;
		$installer->startSetup();

		$data[] = ['status' => 'in_process', 'label' => 'In Process'];
		$data[] = ['status' => 'in_delivery', 'label' => 'In Delivery'];
		$data[] = ['status' => 'delivered', 'label' => 'Delivered'];
		$data[] = ['status' => 'order_canceled', 'label' => 'Order Canceled'];

		$setup->getConnection()->insertArray($setup->getTable('sales_order_status'), ['status', 'label'], $data);

		$setup->getConnection()->insertArray(
			$setup->getTable('sales_order_status_state'),
			['status', 'state', 'is_default', 'visible_on_front'],
			[
				['in_process', 'processing', '0', '1'],
				['in_delivery', 'processing', '0', '1'],
				['delivered', 'processing', '0', '1'],
				['order_canceled', 'canceled', '0', '1'],
			]
		);
		$setup->endSetup();
	}
}
