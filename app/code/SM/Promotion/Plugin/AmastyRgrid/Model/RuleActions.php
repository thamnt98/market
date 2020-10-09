<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Promotion
 *
 * Date: August, 13 2020
 * Time: 2:01 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Promotion\Plugin\AmastyRgrid\Model;

class RuleActions
{
    /**
     * @param \Amasty\Rgrid\Model\RuleActions $subject
     * @param array                           $result
     *
     * @return array
     */
    public function afterToOptionArray(\Amasty\Rgrid\Model\RuleActions $subject, $result)
    {
        $customOptions = [
            'to_fixed' => __('Adjust price to discount value'),
            \SM\Promotion\Model\Data\Rule::TYPE_SETOF_FIXED_DISCOUNT =>
                __('Product Set/Fixed discount for product set'),
            \SM\Promotion\Model\Data\Rule::TYPE_EACH_GROUP_N_DISC_OFF =>
                __('Each Group of N/Fixed Amount Discount: Each 5 items with 20$ off'),
        ];

        $result = array_merge($customOptions, $result);

        return $result;
    }

}
