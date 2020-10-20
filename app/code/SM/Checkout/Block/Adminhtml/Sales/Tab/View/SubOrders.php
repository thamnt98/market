<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace SM\Checkout\Block\Adminhtml\Sales\Tab\View;

/**
 * Class SubOrders
 * @package SM\Checkout\Block\Adminhtml\Sales\Tab\View
 */
class SubOrders extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Sales reorder
     *
     * @var \Magento\Sales\Helper\Reorder
     */
    protected $_salesReorder = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var  \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $collectionFactory
     * @param \Magento\Sales\Helper\Reorder $salesReorder
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $collectionFactory,
        \Magento\Sales\Helper\Reorder $salesReorder,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_salesReorder = $salesReorder;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_sub_orders');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('desc');
        $this->setUseAjax(true);
    }

    /**
     * Apply various selection filters to prepare the sales order grid collection.
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->getReport('sales_order_grid_data_source')->addFieldToSelect(
            'entity_id'
        )->addFieldToSelect(
            'increment_id'
        )->addFieldToSelect(
            'customer_id'
        )->addFieldToSelect(
            'created_at'
        )->addFieldToSelect(
            'grand_total'
        )->addFieldToSelect(
            'order_currency_code'
        )->addFieldToSelect(
            'store_id'
        )->addFieldToSelect(
            'billing_name'
        )->addFieldToSelect(
            'shipping_name'
        )->addFieldToSelect(
            'shipping_information'
        )->addFieldToSelect(
            'store_pick_up'
        )->addFieldToSelect(
            'date'
        )->addFieldToSelect(
            'time'
        )->addFieldToFilter(
            'is_parent',
            0
        )->addFieldToFilter(
            'parent_order',
            $this->getRequest()->getParam('order_id')
        );

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @inheritdoc
     */
    protected function _prepareColumns()
    {
        $this->addColumn('increment_id', ['header' => __('Order #'), 'width' => '100', 'index' => 'increment_id']);

        $this->addColumn(
            'created_at',
            ['header' => __('Purchased'), 'index' => 'created_at', 'type' => 'datetime']
        );

        $this->addColumn('billing_name', ['header' => __('Bill-to Name'), 'index' => 'billing_name']);

        $this->addColumn('shipping_name', ['header' => __('Ship-to Name'), 'index' => 'shipping_name']);

        $this->addColumn(
            'grand_total',
            [
                'header' => __('Order Total'),
                'index' => 'grand_total',
                'type' => 'currency',
                'currency' => 'order_currency_code',
                'rate'  => 1
            ]
        );

        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn(
                'store_id',
                ['header' => __('Purchase Point'), 'index' => 'store_id', 'type' => 'store', 'store_view' => true]
            );
        }

        if ($this->_salesReorder->isAllow()) {
            $this->addColumn(
                'action',
                [
                    'header' => 'Action',
                    'filter' => false,
                    'sortable' => false,
                    'width' => '100px',
                    'renderer' => \Magento\Sales\Block\Adminhtml\Reorder\Renderer\Action::class
                ]
            );
        }

        $this->addColumn('store_pick_up', ['header' => __('Store Pickup'), 'index' => 'store_pick_up']);
        $this->addColumn(
            'date',
            ['header' => __('Delivery Date'), 'index' => 'date', 'type' => 'datetime']
        );
        $this->addColumn('time', ['header' => __('Delivery Time'), 'index' => 'time']);

        return parent::_prepareColumns();
    }

    /**
     * Retrieve the Url for a specified sales order row.
     *
     * @param \Magento\Sales\Model\Order|\Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            'sales/order/view',
            ['order_id' => $row->getId(), 'customer_id' =>  $this->getRequest()->getParam('id')]
        );
    }
}
