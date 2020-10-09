<?php


namespace SM\Promotion\Plugin\Amasty;


class RuleTypeData
{
    /**
     * @param \Amasty\Rules\Helper\Data $object
     * @param \Closure $proceed
     * @param bool $asOptions
     * @return array
     * @see \Amasty\Rules\Helper\Data::getDiscountTypes
     */
    public function aroundGetDiscountTypes(\Amasty\Rules\Helper\Data $object, \Closure $proceed, $asOptions = false)
    {
        $types = $proceed($asOptions);
        if (!$asOptions) {
            foreach ($types as &$typeGroup) {
                /** @var  \Magento\Framework\Phrase $label */
                $label = $typeGroup['label'];
                switch ($label->getText()) {
                    case 'Product Set':
                        $typeGroup['value'][] =
                            [
                                'value' => \SM\Promotion\Model\Data\Rule::TYPE_SETOF_FIXED_DISCOUNT,
                                'label' => __('Fixed discount for product set')
                            ];
                        break;
                    case 'Each Group of N':
                        $typeGroup['value'][] =
                            [
                                'value' => \SM\Promotion\Model\Data\Rule::TYPE_EACH_GROUP_N_DISC_OFF,
                                'label' => __('Fixed Amount Discount: Each 5 items with 20$ off')
                            ];
                        break;
                }
            }
        } else {
            $types[\SM\Promotion\Model\Data\Rule::TYPE_SETOF_FIXED_DISCOUNT] = __('Fixed discount for product set');
            $types[\SM\Promotion\Model\Data\Rule::TYPE_EACH_GROUP_N_DISC_OFF] = __('Fixed Amount Discount: Each 5 items with 20$ off');
        }

        return $types;
    }

    /**
     * @param \Amasty\Rules\Helper\Data $object
     * @param $result
     * @return mixed
     * @see \Amasty\Rules\Helper\Data::staticGetDiscountTypes
     */
    public function afterStaticGetDiscountTypes(\Amasty\Rules\Helper\Data $object, $result)
    {
        $result[\SM\Promotion\Model\Data\Rule::TYPE_SETOF_FIXED_DISCOUNT] = __('Fixed discount for product set');
        $result[\SM\Promotion\Model\Data\Rule::TYPE_EACH_GROUP_N_DISC_OFF] = __('Fixed Amount Discount: Each 5 items with 20$ off');
        return $result;
    }

    public function aroundGetFilePath(\Amasty\Rules\Helper\Data $object, \Closure $proceed, $rule)
    {
        switch ($rule) {
            case \SM\Promotion\Model\Data\Rule::TYPE_SETOF_FIXED_DISCOUNT:
                return \SM\Promotion\Model\Rule\Action\Discount\SetofFixedPrice::class;
            case \SM\Promotion\Model\Data\Rule::TYPE_EACH_GROUP_N_DISC_OFF:
                return \SM\Promotion\Model\Rule\Action\Discount\GroupnDiscOff::class;
            default:
                return $proceed($rule);
        }
    }
}