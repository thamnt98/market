<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_LayeredNavigation
 *
 * Date: April, 27 2020
 * Time: 11:02 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\LayeredNavigation\Model\Category;

class FilterList extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     */
    public function _construct()
    {
        $this->_init(\SM\LayeredNavigation\Model\ResourceModel\Category\FilterList::class);
    }
}
