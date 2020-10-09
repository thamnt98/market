<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Promotion
 *
 * Date: August, 17 2020
 * Time: 4:03 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Promotion\Plugin\AmastyRules\Model\Rule\Action\Discount;

class TheCheapest
{
    /**
     * @var \Amasty\Rules\Helper\Product
     */
    protected $helper;

    /**
     * TheCheapest constructor.
     *
     * @param \Amasty\Rules\Helper\Product $helper
     */
    public function __construct(
        \Amasty\Rules\Helper\Product $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param \Amasty\Rules\Model\Rule\Action\Discount\TheCheapest $subject
     * @param bool                                                 $result
     * @param \Magento\SalesRule\Model\Rule                        $rule
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem         $item
     *
     * @return bool
     */
    public function afterSkip(
        \Amasty\Rules\Model\Rule\Action\Discount\TheCheapest $subject,
        $result,
        $rule,
        $item
    ) {
        if ($rule->getSimpleAction() === \Amasty\Rules\Helper\Data::TYPE_CHEAPEST &&
            !$result &&
            !$this->validateItem($item)
        ) {
            return true;
        }

        return $result;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     *
     * @return bool
     */
    protected function validateItem($item)
    : bool {
        $theCheapestId = $this->getTheCheapestItem($item->getQuote());
        if (!($item instanceof \Magento\Quote\Model\Quote\Item)) {
            $item = $item->getQuoteItem();
        }

        if ($item && $theCheapestId == $item->getItemId()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @return int
     */
    protected function getTheCheapestItem($quote)
    : int {
        if (!$quote) {
            return 0;
        }

        $result = 0;

        foreach ($quote->getAllVisibleItems() as $item) {
            $price = $this->helper->getItemPrice($item);
            if (!isset($min) || $price < $min) {
                $min = $price;
                $result = $item->getItemId();
                continue;
            }
        }

        return (int)$result;
    }
}
