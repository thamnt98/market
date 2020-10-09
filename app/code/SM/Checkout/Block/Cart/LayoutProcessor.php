<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Checkout
 *
 * Date: June, 22 2020
 * Time: 12:43 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Checkout\Block\Cart;

class LayoutProcessor implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface
{

    /**
     * @var \Amasty\Rules\Model\DiscountRegistry
     */
    protected $discountRegistry;
    /**
     * @var \Amasty\Rules\Model\ConfigModel
     */
    protected $configModel;

    public function __construct(
        \Amasty\Rules\Model\DiscountRegistry $discountRegistry,
        \Amasty\Rules\Model\ConfigModel $configModel
    ) {
        $this->discountRegistry = $discountRegistry;
        $this->configModel = $configModel;
    }

    /**
     * @param array $jsLayout
     * @return array
     */
    public function process($jsLayout)
    {
        if ($this->configModel->getShowDiscountBreakdown()) {
            $rulesWithDiscount = $this->discountRegistry->getRulesWithAmount();
            $rulesWithDiscountArray = $this->discountRegistry->convertRulesWithDiscountToArray($rulesWithDiscount);

            $jsLayout['components']['block-totals']['children']['discount']['config']
            ['amount'] = $rulesWithDiscountArray;

            $jsLayout['components']['block-totals']['children']['before_grandtotal']['children']['discount-breakdown']
            ['config'] = [
                'componentDisabled' => true
            ];
        }

        return $jsLayout;
    }
}
