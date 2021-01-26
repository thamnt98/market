<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Catalog
 *
 * Date: January, 13 2021
 * Time: 2:30 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Catalog\Plugin\MagentoCatalog\Block\Product;

class View
{
    /**
     * @param \Magento\Catalog\Block\Product\View\Attributes|\Magento\Catalog\Block\Product\View\Description $subject
     * @param callable                                                                                       $proceed
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function aroundGetProduct($subject, callable $proceed)
    {
        if ($subject->getData('product')) {
            return $subject->getData('product');
        } else {
            return $proceed();
        }
    }
}
