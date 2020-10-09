<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_LayeredNavigation
 *
 * Date: May, 20 2020
 * Time: 11:06 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\LayeredNavigation\Plugin\AmastyShopby\Model\Layer\Filter;

class Attribute
{
    /**
     * @param \Amasty\Shopby\Model\Layer\Filter\Attribute $subject
     * @param                                             $result
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function afterGetName(
        \Amasty\Shopby\Model\Layer\Filter\Attribute $subject,
        $result
    ) {
        try {
            if ($subject->getAttributeModel()->getAttributeCode() === 'is_warehouse') {
                $result = __('Shipping');
            }
        } catch (\Exception $e) {
        }

        return $result;
    }

    /**
     * @param \Amasty\Shopby\Model\Layer\Filter\Attribute $subject
     * @param \Magento\Catalog\Model\Layer\Filter\Item[]  $result
     *
     * @return \Magento\Catalog\Model\Layer\Filter\Item[]
     */
    public function afterGetItems(
        \Amasty\Shopby\Model\Layer\Filter\Attribute $subject,
        $result
    ) {
        try {
            if ($subject->getAttributeModel()->getAttributeCode() === 'is_warehouse' && count($result)) {
                $data = [];
                foreach ($result as $item) {
                    if ($item->getData('value') === 1) {
                        $item->setData('label', __('Same Day'));
                        $data[] = $item;
                        $subject->setItems($data);

                        return $data;
                    }
                }
            }
        } catch (\Exception $e) {
        }

        return $result;
    }
}
