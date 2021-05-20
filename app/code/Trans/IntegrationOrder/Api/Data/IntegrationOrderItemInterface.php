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

namespace Trans\IntegrationOrder\Api\Data;

/**
 * @api
 */
interface IntegrationOrderItemInterface {
	/**
	 * Constants for keys of data array. Identical to the name of the getter in snake case
	 */

	/**
	 * Constant for table name
	 */
	const DEFAULT_EVENT = 'trans_integration';
	const TABLE_NAME    = 'integration_oms_order_item';

	/**
	 * Constant for field table
	 */
	const OMS_ID_ORDER_ITEM     = 'oms_id_order_item';
	const ORDER_ID              = 'order_id';
	const SALES_ORDER_ITEM_ID   = 'sales_order_item_id';
	const PRODUCT_NAME          = 'product_name';
	const PRODUCT_WEIGHT        = 'weight';
	const PRODUCT_HEIGHT        = 'height';
	const PRODUCT_WIDTH         = 'width';
	const PRODUCT_LENGTH        = 'length';
	const PRODUCT_SIZE          = 'size';
	const PRODUCT_DIAMETER      = 'diameter';
	const SKU                   = 'sku';
	const SKU_BASIC             = 'sku_basic';
	const ORIGINAL_PRICE        = 'ori_price';
	const SELLING_PRICE         = 'sell_price';
	const PAID_PRICE            = 'paid_price';
	const DISCOUNT_AMOUNT       = 'disc_price';
	const QTY                   = 'qty';
	const QTY_ALLOCATED         = 'quantity_allocated';
	const ITEM_STATUS           = 'item_status';
	const TOTAL_WEIGHT          = 'total_weight';
	const SUBTOTAL              = 'subtotal';
	const VOUCHER_CODE          = 'voucher_code';
	const PROMO_ID              = 'promo_id';
	const FINAL_TOTAL           = 'final_total';
	const SUBTOTAL_ORDER        = 'subtotal_order';
	const SHIPPING_FEE          = 'shipping_fee';
	const TOTAL_DISCOUNT_AMOUNT = 'total_discount_amount';
	const GRAND_TOTAL           = 'grand_total';
	const PROMOTION_TYPE        = 'promotion_type';
	const PROMOTION_VALUE       = 'promotion_value';
	const IS_FRESH       		= 'is_fresh';

	/**
	 * Get Oms Id Order Item
	 *
	 * @return int
	 */
	public function getOmsIdOrderItem();

	/**
	 * Set Oms Id Order Item
	 *
	 * @param int $omsIdOrderItem
	 * @return void
	 */
	public function setOmsIdOrderItem($omsIdOrderItem);

	/**
	 * Get Order Id
	 *
	 * @param string
	 */
	public function getOrderId();

	/**
	 * Set Order Id
	 *
	 * @param string $orderId
	 * @return void
	 */
	public function setOrderId($orderId);

	/**
	 * Get Sales Order Item Id
	 *
	 * @param int
	 */
	public function getSalesOrderItemId();

	/**
	 * Set Sales Order Item Id
	 *
	 * @param int $salesOrderItemId
	 * @return void
	 */
	public function setSalesOrderItemId($salesOrderItemId);

	/**
	 * Get Product Name
	 *
	 * @param string
	 */
	public function getProductName();

	/**
	 * Set Product Name
	 *
	 * @param string $productName
	 * @return void
	 */
	public function setProductName($productName);

	/**
	 * Get Product Weight
	 *
	 * @param float
	 */
	public function getProductWeight();

	/**
	 * Set Product Weight
	 *
	 * @param float $productWeight
	 * @return void
	 */
	public function setProductWeight($productWeight);

	/**
	 * Get Product Height
	 *
	 * @param float
	 */
	public function getProductHeight();

	/**
	 * Set Product Height
	 *
	 * @param float $productHeight
	 * @return void
	 */
	public function setProductHeight($productHeight);

	/**
	 * Get Product Width
	 *
	 * @param float
	 */
	public function getProductWidth();

	/**
	 * Set Product Width
	 *
	 * @param float $productWidth
	 * @return void
	 */
	public function setProductWidth($productWidth);

	/**
	 * Get Product Length
	 *
	 * @param float
	 */
	public function getProductLength();

	/**
	 * Set Product Length
	 *
	 * @param float $productLength
	 * @return void
	 */
	public function setProductLength($productLength);

	/**
	 * Get Product Size
	 *
	 * @param float
	 */
	public function getProductSize();

	/**
	 * Set Product Size
	 *
	 * @param float $productSize
	 * @return void
	 */
	public function setProductSize($productSize);

	/**
	 * Get Product Diameter
	 *
	 * @param float
	 */
	public function getProductDiameter();

	/**
	 * Set Product Diameter
	 *
	 * @param float $productDiameter
	 * @return void
	 */
	public function setProductDiameter($productDiameter);

	/**
	 * Get SKU
	 *
	 * @param string
	 */
	public function getSKU();

	/**
	 * Set SKU
	 *
	 * @param string $sku
	 * @return void
	 */
	public function setSKU($sku);

	/**
	 * Get SKU Basic
	 *
	 * @param string
	 */
	public function getSkuBasic();

	/**
	 * Set SKU Basic
	 *
	 * @param string $skuBasic
	 * @return void
	 */
	public function setSkuBasic($skuBasic);

	/**
	 * Get Original Price
	 *
	 * @param float
	 */
	public function getOriginalPrice();

	/**
	 * Set Original Price
	 *
	 * @param float $originalPrice
	 * @return void
	 */
	public function setOriginalPrice($originalPrice);

	/**
	 * Get Selling Price
	 *
	 * @param float
	 */
	public function getSellingPrice();

	/**
	 * Set Selling Price
	 *
	 * @param float $sellingPrice
	 * @return void
	 */
	public function setSellingPrice($sellingPrice);

	/**
	 * Get Paid Price
	 *
	 * @param float
	 */
	public function getPaidPrice();

	/**
	 * Set Paid Price
	 *
	 * @param float $paidPrice
	 * @return void
	 */
	public function setPaidPrice($paidPrice);

	/**
	 * Get Discount Amount
	 *
	 * @param float
	 */
	public function getDiscountAmount();

	/**
	 * Set Discount Amount
	 *
	 * @param float $discountAmount
	 * @return void
	 */
	public function setDiscountAmount($discountAmount);

	/**
	 * Get Qty
	 *
	 * @param int
	 */
	public function getQty();

	/**
	 * Set Qty
	 *
	 * @param int $qty
	 * @return void
	 */
	public function setQty($qty);

	/**
	 * Get Qty Allocated
	 *
	 * @param int
	 */
	public function getQtyAllocated();

	/**
	 * Set Qty Allocated
	 *
	 * @param int $qtyAllocated
	 * @return void
	 */
	public function setQtyAllocated($qtyAllocated);

	/**
	 * Get Item Status
	 *
	 * @param int
	 */
	public function getItemStatus();

	/**
	 * Set Item Status
	 *
	 * @param int $itemStatus
	 * @return void
	 */
	public function setItemStatus($itemStatus);

	/**
	 * Get Total Weight
	 *
	 * @param float
	 */
	public function getTotalWeight();

	/**
	 * Set Total Weight
	 *
	 * @param float $totalWeight
	 * @return void
	 */
	public function setTotalWeight($totalWeight);

	/**
	 * Get Subtotal
	 *
	 * @param float
	 */
	public function getSubtotal();

	/**
	 * Set Subtotal
	 *
	 * @param float $subtotal
	 * @return void
	 */
	public function setSubtotal($subtotal);

	/**
	 * Get Voucher Code
	 *
	 * @param string
	 */
	public function getVoucherCode();

	/**
	 * Set Voucher Code
	 *
	 * @param string $voucherCode
	 * @return void
	 */
	public function setVoucherCode($voucherCode);

	/**
	 * Get Promo Id
	 *
	 * @param int
	 */
	public function getPromoId();

	/**
	 * Set Promo Id
	 *
	 * @param int $promoId
	 * @return void
	 */
	public function setPromoId($promoId);

	/**
	 * Get Final Total
	 *
	 * @param float
	 */
	public function getFinalTotal();

	/**
	 * Set Final Total
	 *
	 * @param float $finalTotal
	 * @return void
	 */
	public function setFinalTotal($finalTotal);

	/**
	 * Get Sub Total Order
	 *
	 * @param float
	 */
	public function getSubtotalOrder();

	/**
	 * Set Sub Total Order
	 *
	 * @param float $subtotalOrder
	 * @return void
	 */
	public function setSubtotalOrder($subtotalOrder);

	/**
	 * Get Shipping Fee
	 *
	 * @param float
	 */
	public function getShippingFee();

	/**
	 * Set Shipping Fee
	 *
	 * @param float $shippingFee
	 * @return void
	 */
	public function setShippingFee($shippingFee);

	/**
	 * Get Total Amount Discount
	 *
	 * @param float
	 */
	public function getTotalAmountDiscount();

	/**
	 * Set Total Amount Discount
	 *
	 * @param float $totalAmountDiscount
	 * @return void
	 */
	public function setTotalAmountDiscount($totalAmountDiscount);

	/**
	 * Get Grand Total
	 *
	 * @param float
	 */
	public function getGrandTotal();

	/**
	 * Set Grand Total
	 *
	 * @param float $grandTotal
	 * @return void
	 */
	public function setGrandTotal($grandTotal);

	/**
	 * Get Promotion Type
	 *
	 * @param string
	 */
	public function getPromotionType();

	/**
	 * Set Promotion Type
	 *
	 * @param string $promotionType
	 * @return void
	 */
	public function setPromotionType($promotionType);

	/**
	 * Get Promotion Value
	 *
	 * @param string
	 */
	public function getPromotionValue();

	/**
	 * Set Promotion Value
	 *
	 * @param string $promotionValue
	 * @return void
	 */
	public function setPromotionValue($promotionValue);

	/**
	 * Get Is Fresh
	 *
	 * @return bool
	 */
	public function getIsFresh();

	/**
	 * Set Is Fresh
	 *
	 * @param bool $isFresh
	 * @return bool
	 */
	public function setIsFresh($isFresh);
}
