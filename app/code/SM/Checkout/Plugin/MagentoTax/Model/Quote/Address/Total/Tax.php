<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Checkout
 *
 * Date: July, 22 2020
 * Time: 11:30 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Checkout\Plugin\MagentoTax\Model\Quote\Address\Total;

class Tax
{
    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory
     */
    protected $ruleCollFact;

    /**
     * @var \SM\MyVoucher\Model\AmastyRules\DiscountRegistry
     */
    protected $discountRegistry;

    /**
     * @var \SM\Promotion\Model\Rule\Validator\CustomerUses
     */
    protected $customerUses;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * ShippingDiscount constructor.
     *
     * @param \Magento\Checkout\Model\Session                               $checkoutSession
     * @param \SM\Promotion\Model\Rule\Validator\CustomerUses               $customerUses
     * @param \Amasty\Rules\Model\DiscountRegistry                          $discountRegistry
     * @param \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $ruleCollFact
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \SM\Promotion\Model\Rule\Validator\CustomerUses $customerUses,
        \Amasty\Rules\Model\DiscountRegistry $discountRegistry,
        \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $ruleCollFact
    ) {
        $this->ruleCollFact = $ruleCollFact;
        $this->discountRegistry = $discountRegistry;
        $this->customerUses = $customerUses;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param \Magento\Tax\Model\Sales\Total\Quote\Tax $subject
     * @param \Magento\Tax\Model\Sales\Total\Quote\Tax $result
     * @param \Magento\Quote\Model\Quote                                    $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface           $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total                      $total
     *
     * @return \Magento\Tax\Model\Sales\Total\Quote\Tax
     */
    public function afterCollect(
        \Magento\Tax\Model\Sales\Total\Quote\Tax $subject,
        $result,
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        /** @var \Magento\Quote\Model\Quote\Address $address */
        $address = $shippingAssignment->getShipping()->getAddress();
        if ($isMain = $this->checkoutSession->getMainOrder()) {
            $this->checkoutSession->unsMainOrder();
        }

        if (!$quote->isVirtual() &&
            $address->getAddressType() === 'shipping' &&
            ($address->getShippingAmount() || $total->getData('shipping_amount'))
        ) {
            $addressFreeShip = 0;
            /** @var \Magento\Quote\Model\Quote\Item $item */
            foreach ($address->getAllVisibleItems() as $item) {
                if ($item->getFreeShipping()) {
                    if ($item instanceof \Magento\Quote\Model\Quote\Address\Item) {
                        $ruleIds = $item->getQuoteItem()->getAppliedRuleIds();
                    } else {
                        $ruleIds = $item->getAppliedRuleIds();
                    }

                    $ruleIds = $this->getRuleFreeShipIds($ruleIds, $address);

                    if (empty($ruleIds)) {
                        $item->setFreeShipping(0);
                        if ($item->getQuoteItem()) {
                            $item->getQuoteItem()->setFreeShipping(0);
                        }

                        continue;
                    }

                    $shippingAmount = $total->getTotalAmount('shipping');
                    $baseShippingAmount = $total->getBaseTotalAmount('shipping');
                    $address->setShippingAmount(0);
                    $address->setBaseShippingAmount(0);
                    $address->setShippingInclTax(0);
                    $address->setBaseShippingInclTax(0);
                    $address->setShippingTaxAmount(0);
                    $address->setBaseShippingTaxAmount(0);
                    $address->setShippingDiscountAmount($shippingAmount);
                    $address->setBaseShippingDiscountAmount($baseShippingAmount);
                    $address->setData('shipping_tax_calculation_amount', 0);
                    $address->setData('base_shipping_tax_calculation_amount', 0);
                    $total->setData('shipping_amount', 0)
                        ->setData('base_shipping_amount', 0)
                        ->setData('shipping_incl_tax', 0)
                        ->setData('base_shipping_incl_tax', 0)
                        ->setData('shipping_discount_amount', $shippingAmount)
                        ->setData('base_shipping_discount_amount', $baseShippingAmount);
                    $total->setTotalAmount('shipping', 0);
                    $total->setBaseTotalAmount('shipping', 0);
                    $this->discountRegistry->setFreeShipRules($ruleIds);
                    $this->updateShippingRule($address, $ruleIds)
                        ->updateShippingRule($quote, $ruleIds);
                    $addressFreeShip = 1;
                    break;
                }
            }

            $address->setFreeShipping($addressFreeShip);
        }

        if ($isMain) {
            $this->checkoutSession->setMainOrder(true);
        }

        return $result;
    }

    /**
     * @param $ids
     *
     * @return \Magento\SalesRule\Model\Rule[]
     */
    protected function getRules($ids)
    {
        if (is_array($ids) && !empty($ids)) {
            /** @var \Magento\SalesRule\Model\ResourceModel\Rule\Collection $coll */
            $coll = $this->ruleCollFact->create();
            $coll->addFieldToFilter('rule_id', ['in' => $ids]);

            return $coll->getItems();
        }

        return [];
    }

    /**
     * @param $ruleIds
     * @param \Magento\Quote\Model\Quote\Address $address
     *
     * @return array
     */
    protected function getRuleFreeShipIds($ruleIds, $address)
    {
        $result = [];
        if (!is_array($ruleIds)) {
            $ruleIds = explode(',', $ruleIds);
        }

        $rules = $this->getRules($ruleIds);
        foreach ($rules as $rule) {
            $this->customerUses->unsetAddressRule($rule, $address);
            $customerUses = $this->customerUses->getCustomerUseLeft($rule);
            if (!$customerUses || !$rule->getIsActive()) {
                continue;
            }

            switch ($rule->getSimpleFreeShipping()) {
                case \Magento\OfflineShipping\Model\SalesRule\Rule::FREE_SHIPPING_ITEM:
                    if ($rule->getDiscountQty() === null ||
                        $rule->getDiscountQty() > 0
                    ) {
                        $result[] = $rule->getId();
                        $this->customerUses->setAddressRule($rule, $address);
                    }

                    break;
                case \Magento\OfflineShipping\Model\SalesRule\Rule::FREE_SHIPPING_ADDRESS:
                    $result[] = $rule->getId();
                    $this->customerUses->setAddressRule($rule, $address);
                    break;
            }
        }

        return $result;
    }

    /**
     * @param \Magento\Framework\DataObject $object
     * @param                               $ruleIds
     *
     * @return $this
     */
    protected function updateShippingRule(\Magento\Framework\DataObject $object, $ruleIds)
    {
        $shippingRules = $object->getData(\SM\Promotion\Model\Data\Rule::SHIPPING_RULE_IDS_FIELD);
        if (!is_array($shippingRules)) {
            $shippingRules = explode(',', $shippingRules);
        }

        $shippingRules = array_unique(array_merge($ruleIds, $shippingRules));
        $shippingRules = array_filter($shippingRules, function($value) { return !empty($value); });
        $object->setData(\SM\Promotion\Model\Data\Rule::SHIPPING_RULE_IDS_FIELD, implode(',', $shippingRules));

        return $this;
    }
}
