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

namespace Trans\IntegrationOrder\Model;

use \Trans\IntegrationOrder\Api\Data\IntegrationOrderItemInterface;
use \Trans\IntegrationOrder\Model\ResourceModel\IntegrationOrderItem as ResourceModel;

class IntegrationOrderItem extends \Magento\Framework\Model\AbstractModel implements
IntegrationOrderItemInterface {
	protected function _construct() {
		$this->_init(ResourceModel::class);
	}

	/**
	 * @inheritdoc
	 */
	public function getOmsIdOrderItem() {
		return $this->getData(IntegrationOrderItemInterface::OMS_ID_ORDER_ITEM);
	}

	/**
	 * @inheritdoc
	 */
	public function setOmsIdOrderItem($omsIdOrderItem) {
		return $this->setData(IntegrationOrderItemInterface::OMS_ID_ORDER_ITEM, $omsIdOrderItem);
	}

	/**
	 * @inheritdoc
	 */
	public function getOrderId() {
		return $this->getData(IntegrationOrderItemInterface::ORDER_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setOrderId($orderId) {

		return $this->setData(IntegrationOrderItemInterface::ORDER_ID, $orderId);
	}

	/**
	 * @inheritdoc
	 */
	public function getSalesOrderItemId() {
		return $this->getData(IntegrationOrderItemInterface::SALES_ORDER_ITEM_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setSalesOrderItemId($salesOrderItemId) {
		return $this->setData(IntegrationOrderItemInterface::SALES_ORDER_ITEM_ID, $salesOrderItemId);
	}

	/**
	 * @inheritdoc
	 */
	public function getProductName() {
		return $this->getData(IntegrationOrderItemInterface::PRODUCT_NAME);
	}

	/**
	 * @inheritdoc
	 */
	public function setProductName($productName) {
		return $this->setData(IntegrationOrderItemInterface::PRODUCT_NAME, $productName);
	}

	/**
	 * @inheritdoc
	 */
	public function getProductWeight() {
		return $this->getData(IntegrationOrderItemInterface::PRODUCT_WEIGHT);
	}

	/**
	 * @inheritdoc
	 */
	public function setProductWeight($productWeight) {
		return $this->setData(IntegrationOrderItemInterface::PRODUCT_WEIGHT, $productWeight);
	}

	/**
	 * @inheritdoc
	 */
	public function getProductHeight() {
		return $this->getData(IntegrationOrderItemInterface::PRODUCT_HEIGHT);
	}

	/**
	 * @inheritdoc
	 */
	public function setProductHeight($productHeight) {
		return $this->setData(IntegrationOrderItemInterface::PRODUCT_HEIGHT, $productHeight);
	}

	/**
	 * @inheritdoc
	 */
	public function getProductWidth() {
		return $this->getData(IntegrationOrderItemInterface::PRODUCT_WIDTH);
	}

	/**
	 * @inheritdoc
	 */
	public function setProductWidth($productWidth) {
		return $this->setData(IntegrationOrderItemInterface::PRODUCT_WIDTH, $productWidth);
	}

	/**
	 * @inheritdoc
	 */
	public function getProductLength() {
		return $this->getData(IntegrationOrderItemInterface::PRODUCT_LENGTH);
	}

	/**
	 * @inheritdoc
	 */
	public function setProductLength($productLength) {
		return $this->setData(IntegrationOrderItemInterface::PRODUCT_LENGTH, $productLength);
	}

	/**
	 * @inheritdoc
	 */
	public function getProductSize() {
		return $this->getData(IntegrationOrderItemInterface::PRODUCT_SIZE);
	}

	/**
	 * @inheritdoc
	 */
	public function setProductSize($productSize) {
		return $this->setData(IntegrationOrderItemInterface::PRODUCT_SIZE, $productSize);
	}

	/**
	 * @inheritdoc
	 */
	public function getProductDiameter() {
		return $this->getData(IntegrationOrderItemInterface::PRODUCT_DIAMETER);
	}

	/**
	 * @inheritdoc
	 */
	public function setProductDiameter($productDiameter) {
		return $this->setData(IntegrationOrderItemInterface::PRODUCT_DIAMETER, $productDiameter);
	}

	/**
	 * @inheritdoc
	 */
	public function getSKU() {
		return $this->getData(IntegrationOrderItemInterface::SKU);
	}

	/**
	 * @inheritdoc
	 */
	public function setSKU($sku) {
		return $this->setData(IntegrationOrderItemInterface::SKU, $sku);
	}

	/**
	 * @inheritdoc
	 */
	public function getSkuBasic() {
		return $this->getData(IntegrationOrderItemInterface::SKU_BASIC);
	}

	/**
	 * @inheritdoc
	 */
	public function setSkuBasic($skuBasic) {
		return $this->setData(IntegrationOrderItemInterface::SKU_BASIC, $skuBasic);
	}

	/**
	 * @inheritdoc
	 */
	public function getOriginalPrice() {
		return $this->getData(IntegrationOrderItemInterface::ORIGINAL_PRICE);
	}

	/**
	 * @inheritdoc
	 */
	public function setOriginalPrice($originalPrice) {
		return $this->setData(IntegrationOrderItemInterface::ORIGINAL_PRICE, $originalPrice);
	}

	/**
	 * @inheritdoc
	 */
	public function getSellingPrice() {
		return $this->getData(IntegrationOrderItemInterface::SELLING_PRICE);
	}

	/**
	 * @inheritdoc
	 */
	public function setSellingPrice($sellingPrice) {
		return $this->setData(IntegrationOrderItemInterface::SELLING_PRICE, $sellingPrice);
	}

	/**
	 * @inheritdoc
	 */
	public function getPaidPrice() {
		return $this->getData(IntegrationOrderItemInterface::PAID_PRICE);
	}

	/**
	 * @inheritdoc
	 */
	public function setPaidPrice($paidPrice) {
		return $this->setData(IntegrationOrderItemInterface::PAID_PRICE, $paidPrice);
	}

	/**
	 * @inheritdoc
	 */
	public function getDiscountAmount() {
		return $this->getData(IntegrationOrderItemInterface::DISCOUNT_AMOUNT);
	}

	/**
	 * @inheritdoc
	 */
	public function setDiscountAmount($discountAmount) {
		return $this->setData(IntegrationOrderItemInterface::DISCOUNT_AMOUNT, $discountAmount);
	}

	/**
	 * @inheritdoc
	 */
	public function getQty() {
		return $this->getData(IntegrationOrderItemInterface::QTY);
	}

	/**
	 * @inheritdoc
	 */
	public function setQty($qty) {
		return $this->setData(IntegrationOrderItemInterface::QTY, $qty);
	}

	/**
	 * @inheritdoc
	 */
	public function getQtyAllocated() {
		return $this->getData(IntegrationOrderItemInterface::QTY_ALLOCATED);
	}

	/**
	 * @inheritdoc
	 */
	public function setQtyAllocated($qtyAllocated) {
		return $this->setData(IntegrationOrderItemInterface::QTY_ALLOCATED, $qtyAllocated);
	}

	/**
	 * @inheritdoc
	 */
	public function getItemStatus() {
		return $this->getData(IntegrationOrderItemInterface::ITEM_STATUS);
	}

	/**
	 * @inheritdoc
	 */
	public function setItemStatus($itemStatus) {
		return $this->setData(IntegrationOrderItemInterface::ITEM_STATUS, $itemStatus);
	}

	/**
	 * @inheritdoc
	 */
	public function getTotalWeight() {
		return $this->getData(IntegrationOrderItemInterface::TOTAL_WEIGHT);
	}

	/**
	 * @inheritdoc
	 */
	public function setTotalWeight($totalWeight) {
		return $this->setData(IntegrationOrderItemInterface::TOTAL_WEIGHT, $totalWeight);
	}

	/**
	 * @inheritdoc
	 */
	public function getSubtotal() {
		return $this->getData(IntegrationOrderItemInterface::SUBTOTAL);
	}

	/**
	 * @inheritdoc
	 */
	public function setSubtotal($subtotal) {
		return $this->setData(IntegrationOrderItemInterface::SUBTOTAL, $subtotal);
	}

	/**
	 * @inheritdoc
	 */
	public function getVoucherCode() {
		return $this->getData(IntegrationOrderItemInterface::VOUCHER_CODE);
	}

	/**
	 * @inheritdoc
	 */
	public function setVoucherCode($voucherCode) {
		return $this->setData(IntegrationOrderItemInterface::VOUCHER_CODE, $voucherCode);
	}

	/**
	 * @inheritdoc
	 */
	public function getPromoId() {
		return $this->getData(IntegrationOrderItemInterface::PROMO_ID);
	}

	/**
	 * @inheritdoc
	 */
	public function setPromoId($promoId) {
		return $this->setData(IntegrationOrderItemInterface::PROMO_ID, $promoId);
	}

	/**
	 * @inheritdoc
	 */
	public function getFinalTotal() {
		return $this->getData(IntegrationOrderItemInterface::FINAL_TOTAL);
	}

	/**
	 * @inheritdoc
	 */
	public function setFinalTotal($finalTotal) {
		return $this->setData(IntegrationOrderItemInterface::FINAL_TOTAL, $finalTotal);
	}

	/**
	 * @inheritdoc
	 */
	public function getSubtotalOrder() {
		return $this->getData(IntegrationOrderItemInterface::SUBTOTAL_ORDER);
	}

	/**
	 * @inheritdoc
	 */
	public function setSubtotalOrder($subtotalOrder) {
		return $this->setData(IntegrationOrderItemInterface::SUBTOTAL_ORDER, $subtotalOrder);
	}

	/**
	 * @inheritdoc
	 */
	public function getShippingFee() {
		return $this->getData(IntegrationOrderItemInterface::SHIPPING_FEE);
	}

	/**
	 * @inheritdoc
	 */
	public function setShippingFee($shippingFee) {
		return $this->setData(IntegrationOrderItemInterface::SHIPPING_FEE, $shippingFee);
	}

	/**
	 * @inheritdoc
	 */
	public function getTotalAmountDiscount() {
		return $this->getData(IntegrationOrderItemInterface::TOTAL_DISCOUNT_AMOUNT);
	}

	/**
	 * @inheritdoc
	 */
	public function setTotalAmountDiscount($totalAmountDiscount) {
		return $this->setData(IntegrationOrderItemInterface::TOTAL_DISCOUNT_AMOUNT, $totalAmountDiscount);
	}

	/**
	 * @inheritdoc
	 */
	public function getGrandTotal() {
		return $this->getData(IntegrationOrderItemInterface::GRAND_TOTAL);
	}

	/**
	 * @inheritdoc
	 */
	public function setGrandTotal($grandTotal) {
		return $this->setData(IntegrationOrderItemInterface::GRAND_TOTAL, $grandTotal);
	}

	/**
	 * @inheritdoc
	 */
	public function getPromotionType() {
		return $this->getData(IntegrationOrderItemInterface::PROMOTION_TYPE);
	}

	/**
	 * @inheritdoc
	 */
	public function setPromotionType($promotionType) {
		return $this->setData(IntegrationOrderItemInterface::PROMOTION_TYPE, $promotionType);
	}

	/**
	 * @inheritdoc
	 */
	public function getPromotionValue() {
		return $this->getData(IntegrationOrderItemInterface::PROMOTION_VALUE);
	}

	/**
	 * @inheritdoc
	 */
	public function setPromotionValue($promotionValue) {
		return $this->setData(IntegrationOrderItemInterface::PROMOTION_VALUE, $promotionValue);
	}

	/**
	 * @inheritdoc
	 */
	public function getIsFresh() {
		return $this->getData(IntegrationOrderItemInterface::IS_FRESH);
	}

	/**
	 * @inheritdoc
	 */
	public function setIsFresh($isFresh) {
		return $this->setData(IntegrationOrderItemInterface::IS_FRESH, $isFresh);
	}
}
