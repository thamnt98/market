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

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface {
	/**
	 * Upgrade DB schema for a module
	 *
	 * @param ModuleDataSetupInterface $setup
	 * @param ModuleContextInterface $context
	 * @return void
	 */
	public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
		$installer = $setup;
		$installer->startSetup();

		$omsStatus          = $setup->getTable('integration_oms_status');
		$magentoOrderStatus = $setup->getTable('sales_order_status');
		$magentoOrderState  = $setup->getTable('sales_order_status_state');
		if (version_compare($context->getVersion(), '1.0.7', '<')) {
			if ($setup->getConnection()->isTableExists($omsStatus) == true) {
				$data = [
					[2, 2, 1, '11', 'In Process', '01', 'Order is Picking Pending at Transmart', '', ''],
					[2, 2, 2, '11', 'In Process', '11', 'Order is being picked by Transmart', '', ''],
					[2, 2, 3, '11', 'In Process', '21', 'Order has been successfully picked by Transmart', '', ''],
					[2, 2, 4, '11', 'In Process', '91', 'Order has been Rejected by Transmart', '', ''],
					[2, 2, 5, '11', 'In Process', '31', 'Order is being packed by Transmart', '', ''],
					[2, 2, 6, '11', 'In Process', '41', 'Order has been successfully packed by Transmart', '', ''],
					[2, 2, 99, '91', 'Order Canceled', '91', 'Order has been cancelled by Transmart', '', ''],
					[2, 3, '', '21', 'In Process', '01', 'Waiting for being Picked up by Courier', '', ''],
					[3, 3, '', '21', 'In Process', '11', 'Order is Ready to be Picked up by Courier', '', ''],
					[3, 3, 3, '31', 'In Delivery', '01', 'Order has been successfully Picked up by Courier', '', ''],
					[3, 4, '', '31', 'In Delivery', '11', 'Order is being Delivered by Courier', '', ''],
					[3, 98, '', '31', 'In Delivery', '71', 'Received AWB Number :', '', ''],
					[4, 4, 2, '31', 'In Delivery', '21', 'Order is in Transit', '', ''],
					[4, 5, '', '41', 'Delivered', '01', 'Order has been Successfully Delivered into Destination', '', ''],
					['', '', '', '01', 'Waiting For Payment', '01', 'Waiting for Payment', '1', ''],
					['', '', '', '01', 'Waiting For Payment', '81', 'Payment Success. Order has been successfully Paid', '2', '00'],
					['', '', '', '01', 'Waiting For Payment', '91', 'Payment Failed. Order will be Cancelled', '99', '01....06'],
					[1, 2, '', '11', 'In Process', '02', 'Payment Success. Order is in Process', '', ''],
					[1, 99, '', '91', 'In Process', '91', 'Order has been cancelled by Transmart', '', ''],
					[2, 99, '', '91', 'Order Canceled', '91', 'Order has been cancelled by Transmart', '', ''],
					[3, 99, '', '91', 'Order Canceled', '91', 'Order has been cancelled by Transmart', '', ''],
					[4, 4, 3, '31', 'In Delivery', '31', 'Failed Delivery', '', ''],
					[4, 99, '', '91', 'Order Canceled', '91', 'Order has been cancelled by Transmart', '', ''],
					[3, 5, '', '41', 'Delivered', '02', 'Order has been Successfully Picked up by Customer', '', ''],

				];
				$columns = ['status', 'action', 'sub_action', 'fe_status_no', 'fe_status',
					'fe_sub_status_no', 'fe_sub_status', 'oms_payment_status', 'pg_status_no'];
				$setup->getConnection()->insertArray($omsStatus, $columns, $data);
			}
			$setup->endSetup();
		}

		if (version_compare($context->getVersion(), '1.1.0', '<')) {
			if ($setup->getConnection()->isTableExists($omsStatus) == true) {
				$whereConditions = [
					$setup->getConnection()->quoteInto(
						'fe_sub_status = ?', 'Order has been successfully packed by Transmart'),
				];
				$setup->getConnection()->delete($omsStatus, $whereConditions);
			}
			if ($setup->getConnection()->isTableExists($omsStatus) == true) {
				$whereConditions = [
					$setup->getConnection()->quoteInto(
						'fe_sub_status = ?', 'Order has been Rejected by Transmart'),
				];
				$setup->getConnection()->delete($omsStatus, $whereConditions);
			}
			if ($setup->getConnection()->isTableExists($omsStatus) == true) {
				$whereConditions = [
					$setup->getConnection()->quoteInto(
						'fe_sub_status = ?', 'Order is being packed by Transmart'),
				];
				$setup->getConnection()->delete($omsStatus, $whereConditions);
			}
			// remove AWB
			if ($setup->getConnection()->isTableExists($omsStatus) == true) {
				$whereConditions = [
					$setup->getConnection()->quoteInto(
						'fe_sub_status = ?', 'Received AWB Number :'),
				];
				$setup->getConnection()->delete($omsStatus, $whereConditions);
			}
			if ($setup->getConnection()->isTableExists($omsStatus) == true) {
				$data = [
					[2, 2, 6, '11', 'In Process', '31', 'Order is being packed by Transmart', '', ''],
					[2, 2, 7, '11', 'In Process', '41', 'Order has been successfully packed by Transmart', '', ''],
					[2, 3, '', '51', 'Pick up by Customer', '01', 'Ready to Pick up by Customer', '', ''],
					[3, 3, 1, '21', 'In Process', '21', 'Waiting for being Picked up by Customer', '', ''],
					[3, 3, 2, '21', 'In Process', '31', 'Waiting for being Picked up by Courier', '', ''],
					[4, 4, 1, '31', 'In Delivery', '41', 'Out for Delivery', '', ''],

				];
				$columns = ['status', 'action', 'sub_action', 'fe_status_no', 'fe_status',
					'fe_sub_status_no', 'fe_sub_status', 'oms_payment_status', 'pg_status_no'];
				$setup->getConnection()->insertArray($omsStatus, $columns, $data);
			}
			if ($setup->getConnection()->isTableExists($magentoOrderStatus) == true) {
				$data = [
					['pick_up_by_customer', 'Pick up by Customer'],

				];
				$columns = ['status', 'label'];
				$setup->getConnection()->insertArray($magentoOrderStatus, $columns, $data);
			}
			if ($setup->getConnection()->isTableExists($magentoOrderState) == true) {
				$data = [
					['pick_up_by_customer', 'processing', 1, 1],

				];
				$columns = ['status', 'state', 'is_default', 'visible_on_front'];
				$setup->getConnection()->insertArray($magentoOrderState, $columns, $data);
			}

			$setup->endSetup();
		}

		if (version_compare($context->getVersion(), '1.1.1', '<')) {
			if ($setup->getConnection()->isTableExists($omsStatus) == true) {
				$whereConditions = [
					$setup->getConnection()->quoteInto(
						'fe_sub_status = ?', 'Order is in Transit'),
				];
				$setup->getConnection()->delete($omsStatus, $whereConditions);
			}

			if ($setup->getConnection()->isTableExists($omsStatus) == true) {
				$whereConditions = [
					$setup->getConnection()->quoteInto(
						'fe_status_no = ?', '21'),
				];
				$setup->getConnection()->delete($omsStatus, $whereConditions);
			}

			if ($setup->getConnection()->isTableExists($omsStatus) == true) {
				$data = [
					[4, 4, 2, '32', 'In Transit', '01', 'Order is in Transit', '', ''],
					[2, 3, '', '21', 'In Process - Waiting For Pickup', '01', 'Waiting for being Picked up by Courier', '', ''],
					[3, 3, '', '21', 'In Process - Waiting For Pickup', '11', 'Order is Ready to be Picked up by Courier', '', ''],
					[3, 3, 1, '21', 'In Process - Waiting For Pickup', '21', 'Waiting for being Picked up by Customer', '', ''],
					[3, 3, 2, '21', 'In Process - Waiting For Pickup', '31', 'Waiting for being Picked up by Courier', '', ''],

				];
				$columns = ['status', 'action', 'sub_action', 'fe_status_no', 'fe_status',
					'fe_sub_status_no', 'fe_sub_status', 'oms_payment_status', 'pg_status_no'];
				$setup->getConnection()->insertArray($omsStatus, $columns, $data);
			}

			if ($setup->getConnection()->isTableExists($magentoOrderStatus) == true) {
				$data = [
					['in_process_waiting_for_pickup', 'In Process - Waiting For Pickup'],
					['in_transit', 'In Transit'],

				];
				$columns = ['status', 'label'];
				$setup->getConnection()->insertArray($magentoOrderStatus, $columns, $data);
			}

			if ($setup->getConnection()->isTableExists($magentoOrderState) == true) {
				$data = [
					['in_process_waiting_for_pickup', 'processing', 1, 1],
					['in_transit', 'processing', 1, 1],

				];
				$columns = ['status', 'state', 'is_default', 'visible_on_front'];
				$setup->getConnection()->insertArray($magentoOrderState, $columns, $data);
			}

			$setup->endSetup();
		}

		if (version_compare($context->getVersion(), '1.1.6', '<')) {
			if ($setup->getConnection()->isTableExists($omsStatus) == true) {
				$data = [
					[4, 4, 3, '31', 'Failed Delivery', '31', 'Failed to Delivery', '', ''],
				];
				$columns = ['status', 'action', 'sub_action', 'fe_status_no', 'fe_status',
					'fe_sub_status_no', 'fe_sub_status', 'oms_payment_status', 'pg_status_no'];
				$setup->getConnection()->insertArray($omsStatus, $columns, $data);
			}

			if ($setup->getConnection()->isTableExists($magentoOrderStatus) == true) {
				$data = [
					['failed_delivery', 'Failed Delivery'],
				];
				$columns = ['status', 'label'];
				$setup->getConnection()->insertArray($magentoOrderStatus, $columns, $data);
			}

			if ($setup->getConnection()->isTableExists($magentoOrderState) == true) {
				$data = [
					['failed_delivery', 'in_delivery', 1, 1],
				];
				$columns = ['status', 'state', 'is_default', 'visible_on_front'];
				$setup->getConnection()->insertArray($magentoOrderState, $columns, $data);
			}

			$setup->endSetup();
		}
	}
}
