<?php

/**
 * @category SM
 * @package SM_Brand
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author      Chinhvd <chinhvd@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Brand\Block\Adminhtml\Category\Tab\BrandList;

use Amasty\ShopbyBrand\Model\ResourceModel\Slider\Grid\Collection;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry = null;

    /**
     * @var \SM\Brand\Model\Category\BrandListFactory
     */
    protected $brandListFactory;

    /**
     * @var Collection
     */
    protected $sliderCollection;

    /**
     * @param Collection $sliderCollection
     * @param \SM\Brand\Model\Category\BrandListFactory $brandListFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        Collection $sliderCollection,
        \SM\Brand\Model\Category\BrandListFactory $brandListFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->registry = $registry;
        parent::__construct($context, $backendHelper, $data);
        $this->brandListFactory = $brandListFactory;
        $this->sliderCollection = $sliderCollection;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('catalog_category_brand_list');
        $this->setDefaultSort('option_setting_id');
        $this->setUseAjax(true);
        $this->setPagerVisibility(false);
    }

    /**
     * @return \Magento\Catalog\Api\Data\CategoryInterface|null
     */
    public function getCategory()
    {
        return $this->registry->registry('category');
    }

    /**
     * @return \SM\Brand\Block\Adminhtml\Category\Tab\BrandList\Grid
     * @throws \Exception
     */
    protected function _prepareCollection()
    {
        /** @var \SM\Brand\Model\ResourceModel\Category\BrandList\Collection $collection */
        $collection = $this->brandListFactory->create()->getCollection();
        $collection->addFieldToFilter('category_id', $this->getCategory()->getId())
            ->addOrder('position', 'asc');
        if ($this->getParam('selected')) {
            $this->addItemsBySelected($collection);
        }

        $this->setCollection($collection);
        $idx = 0;
        foreach ($collection as $item) {
            $item->setPosition($idx);
            $idx++;
        }

        return $this;
    }

    /**
     * @param \SM\Brand\Model\ResourceModel\Category\BrandList\Collection $collection
     * @throws \Exception
     */
    protected function addItemsBySelected($collection)
    {
        $selected = $this->getParam('selected');
        $collection->load()->removeAllItems();
        $index = 0;

        foreach ($selected as $code => $position) {
            $slider = $this->sliderCollection->getItemById($code);
            $collection->addItem(new \Magento\Framework\DataObject([
                'id'                 => $index++,
                'option_setting_id'  => $code,
                'position'           => $position,
                'title'              => $slider->getData('title')
            ]));
        }

        $collection->setSize($index);
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        $this->addColumn(
            'draggable-position',
            [
                'renderer'   => \Magento\Backend\Block\Widget\Grid\Column\Renderer\DraggableHandle::class,
                'index'      => 'option_setting_id',
                'inline_css' => 'draggable-handle',
            ]
        );

        $this->addColumn('title', ['header' => __('Title'), 'index' => 'title']);
        $this->addColumn(
            'position',
            [
                'header'   => __('Position'),
                'type'     => 'number',
                'index'    => 'position',
                'editable' => true,
                'renderer' => \Magento\VisualMerchandiser\Block\Adminhtml\Widget\Grid\Column\Renderer\Position::class
            ]
        );
        $this->addColumn(
            'action',
            [
                'header'   => __('Action'),
                'index'    => 'option_setting_id',
                'renderer' => \Magento\Backend\Block\Widget\Grid\Column\Renderer\Action::class,
                'filter'   => false,
                'sortable' => false,
                'actions'  => [
                    [
                        'caption' => __('Unassign'),
                        'url'     => '#',
                        'name'    => 'unassign'
                    ],
                ]
            ]
        );
        $this->getColumnSet()->setSortable(false);
        $this->setFilterVisibility(false);

        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('sm_brand/*/grid', ['_current' => true]);
    }

    /**
     * @return array
     */
    protected function getSelected()
    {
        $result = $this->getRequest()->getPost('selected_brand_list');
        if ($result === null) {
            $category = $this->getCategory();
            /** @var \SM\Brand\Model\ResourceModel\Category\BrandList\Collection $collection */
            $collection = $this->brandListFactory->create()
                ->getCollection()
                ->getSelect()
                ->where("category_id = ?", $category->getId());

            return $collection->getColumnValues('option_setting_id');
        }

        return $result;
    }

    /**
     * @return \Magento\Framework\App\RequestInterface
     */
    public function getRequest()
    {
        return $this->_request;
    }
}
