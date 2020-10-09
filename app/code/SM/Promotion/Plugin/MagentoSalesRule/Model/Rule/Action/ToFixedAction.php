<?php


namespace SM\Promotion\Plugin\MagentoSalesRule\Model\Rule\Action;


use Magento\SalesRule\Model\Rule;

class ToFixedAction
{
    /**
     * @param Rule\Action\SimpleActionOptionsProvider $subject
     * @param $result
     * @see \Magento\SalesRule\Model\Rule\Action\SimpleActionOptionsProvider::toOptionArray
     */
    public function afterToOptionArray(
        \Magento\SalesRule\Model\Rule\Action\SimpleActionOptionsProvider $subject,
        $result
    ) {
        $result[] = ['label' => __('Adjust price to discount value'), 'value' => Rule::TO_FIXED_ACTION];
        return $result;
    }
}