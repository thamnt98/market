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

namespace SM\LayeredNavigation\Model\ResourceModel\Category\FilterList;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            \SM\LayeredNavigation\Model\Category\FilterList::class,
            \SM\LayeredNavigation\Model\ResourceModel\Category\FilterList::class
        );
    }

    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()
            ->joinLeft(
                ['attr' => $this->getResource()->getTable('eav_attribute')],
                'main_table.attribute_code = attr.attribute_code',
                [
                    'attribute_label' => 'attr.frontend_label'
                ]
            );

        return $this;
    }

    /**
     * @param $size
     *
     * @return $this
     */
    public function setSize($size)
    {
        $this->_totalRecords = $size;

        return $this;
    }
}
