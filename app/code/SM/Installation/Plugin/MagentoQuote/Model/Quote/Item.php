<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Installation
 *
 * Date: May, 16 2020
 * Time: 11:33 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Installation\Plugin\MagentoQuote\Model\Quote;

use SM\Installation\Helper\Data as Helper;

class Item
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * Item constructor.
     *
     * @param Helper $helper
     */
    public function __construct(
        Helper $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $subject
     * @param array                           $result
     *
     * @return array
     */
    public function afterToArray(
        \Magento\Quote\Model\Quote\Item $subject,
        $result
    ) {
        try {
            if ($this->helper->isEnabled(\Magento\Store\Model\ScopeInterface::SCOPE_STORE, $subject->getStoreId())) {
                $buyRequest = $subject->getOptionByCode('info_buyRequest');
                if ($buyRequest) {
                    $value = \Zend_Json_Decoder::decode($buyRequest->getValue());
                    $result[Helper::QUOTE_OPTION_KEY] = $value[Helper::QUOTE_OPTION_KEY] ?? [];
                }
            }
        } catch (\Exception $e) {
        }

        return $result;
    }
}
