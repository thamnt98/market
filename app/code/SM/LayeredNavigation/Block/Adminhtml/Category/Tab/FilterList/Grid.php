<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_LayeredNavigation
 *
 * Date: April, 27 2020
 * Time: 3:05 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\LayeredNavigation\Block\Adminhtml\Category\Tab\FilterList;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry = null;

    /**
     * @var \SM\LayeredNavigation\Model\Category\FilterListFactory
     */
    protected $filterListFactory;

    /**
     * @var \SM\LayeredNavigation\Helper\Data\FilterList
     */
    protected $helper;

    /**
     * @param \SM\LayeredNavigation\Helper\Data\FilterList           $helper
     * @param \SM\LayeredNavigation\Model\Category\FilterListFactory $filterListFactory
     * @param \Magento\Backend\Block\Template\Context                $context
     * @param \Magento\Backend\Helper\Data                           $backendHelper
     * @param \Magento\Framework\Registry                            $registry
     * @param array                                                  $data
     */
    public function __construct(
        \SM\LayeredNavigation\Helper\Data\FilterList $helper,
        \SM\LayeredNavigation\Model\Category\FilterListFactory $filterListFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->registry = $registry;
        parent::__construct($context, $backendHelper, $data);
        $this->filterListFactory = $filterListFactory;
        $this->helper = $helper;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('catalog_category_filter_list');
        $this->setDefaultSort('attribute_code');
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
     * @return $this
     * @throws \Exception
     */
    protected function _prepareCollection()
    {
        /** @var \SM\LayeredNavigation\Model\ResourceModel\Category\FilterList\Collection $collection */
        $collection = $this->filterListFactory->create()->getCollection();
        $collection->addFieldToFilter('category_id', $this->getCategory()->getId())
            ->addOrder('position', 'asc');
        if ($this->getParam('selected')) {
            $this->addItemsBySelected($collection);
        }

        $this->setCollection($collection);
        $idx = 0;
        foreach ($collection as $item) {
            $options = $this->helper->getAllOptions();
            if (empty($item->getAttributeLabel())) {
                $item->setAttributeLabel($options[$item->getAttributeCode()]['frontend_label'] ?? '');
            }

            $item->setPosition($idx);
            $idx++;
        }

        return $this;
    }

    /**
     * @param \SM\LayeredNavigation\Model\ResourceModel\Category\FilterList\Collection $collection
     *
     * @throws \Exception
     */
    protected function addItemsBySelected($collection)
    {
        $selected = $this->getParam('selected');
        $options = $this->helper->getAllOptions();
        $collection->load()->removeAllItems();
        $index = 0;
        foreach ($selected as $code => $position) {
            if (!key_exists($code, $options)) {
                continue;
            }

            $collection->addItem(new \Magento\Framework\DataObject([
                'id'              => $index++,
                'attribute_code'  => $code,
                'position'        => $position,
                'attribute_label' => $options[$code]['frontend_label'] ?? ''
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
                'index'      => 'attribute_code',
                'inline_css' => 'draggable-handle',
            ]
        );

        $this->addColumn('attribute_label', ['header' => __('Name'), 'index' => 'attribute_label']);
        $this->addColumn('attribute_code', ['header' => __('Code'), 'index' => 'attribute_code']);
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
                'index'    => 'attribute_code',
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
        return $this->getUrl('smlayer/*/grid', ['_current' => true]);
    }

    /**
     * @return array
     */
    protected function getSelected()
    {
        $result = $this->getRequest()->getPost('selected_filter_list');
        if ($result === null) {
            $category = $this->getCategory();
            /** @var \SM\LayeredNavigation\Model\ResourceModel\Category\FilterList\Collection $collection */
            $collection = $this->filterListFactory->create()
                ->getCollection()
                ->getSelect()
                ->where("category_id = ?", $category->getId());

            return $collection->getColumnValues('attribute_code');
        }

        return $result;
    }

    /**
     * @return \Magento\Framework\App\Request\Http
     */
    public function getRequest()
    {
        return $this->_request;
    }
}
