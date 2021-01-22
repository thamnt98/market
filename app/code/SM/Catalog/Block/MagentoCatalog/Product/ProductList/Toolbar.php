<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Catalog
 *
 * Date: June, 02 2020
 * Time: 1:41 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Catalog\Block\MagentoCatalog\Product\ProductList;

class Toolbar extends \Magento\Catalog\Block\Product\ProductList\Toolbar
{
    /**
     * @var \SM\Catalog\Helper\ProductList\Toolbar
     */
    protected $helper;

    /**
     * @var \Magento\Catalog\Model\Product\ProductList\ToolbarMemorizer
     */
    protected $toolbarMemorizer;

    /**
     * @var \Magento\Framework\App\Http\Context|null
     */
    protected $httpContext;

    /**
     * Toolbar constructor.
     *
     * @param \SM\Catalog\Helper\ProductList\Toolbar                           $helper
     * @param \Magento\Framework\View\Element\Template\Context                 $context
     * @param \Magento\Catalog\Model\Session                                   $catalogSession
     * @param \Magento\Catalog\Model\Config                                    $catalogConfig
     * @param \Magento\Catalog\Model\Product\ProductList\Toolbar               $toolbarModel
     * @param \Magento\Framework\Url\EncoderInterface                          $urlEncoder
     * @param \Magento\Catalog\Helper\Product\ProductList                      $productListHelper
     * @param \Magento\Framework\Data\Helper\PostHelper                        $postDataHelper
     * @param array                                                            $data
     * @param \Magento\Catalog\Model\Product\ProductList\ToolbarMemorizer|null $toolbarMemorizer
     * @param \Magento\Framework\App\Http\Context|null                         $httpContext
     * @param \Magento\Framework\Data\Form\FormKey|null                        $formKey
     */
    public function __construct(
        \SM\Catalog\Helper\ProductList\Toolbar $helper,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Catalog\Model\Product\ProductList\Toolbar $toolbarModel,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Catalog\Helper\Product\ProductList $productListHelper,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Product\ProductList\ToolbarMemorizer $toolbarMemorizer,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Framework\Data\Form\FormKey $formKey,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $catalogSession,
            $catalogConfig,
            $toolbarModel,
            $urlEncoder,
            $productListHelper,
            $postDataHelper,
            $data,
            $toolbarMemorizer,
            $httpContext,
            $formKey
        );
        $this->helper = $helper;
        $this->toolbarMemorizer = $toolbarMemorizer;
        $this->httpContext = $httpContext;
    }

    /**
     * @override
     *
     * @param \Magento\Framework\Data\Collection $collection
     *
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;

        $this->_collection->setCurPage($this->getCurrentPage());

        // we need to set pagination only if passed value integer and more that 0
        $limit = (int)$this->getLimit();
        if ($limit) {
            $this->_collection->setPageSize($limit);
        }

        $requestOrder = $this->toolbarMemorizer->getOrder();
        $order = $this->isOrderByPrice($requestOrder) ? 'price' : $requestOrder;
        $customDir = \SM\Catalog\Helper\ProductList\Toolbar::getDirection();
        $method = $this->helper->getAddSortByMethodName($order);
        if (key_exists($requestOrder, $customDir) && method_exists($this->helper, $method)) {
            $dir = $customDir[$requestOrder];
            $this->helper->{$method}($collection, $dir);
        } else {
            if ($currentOrder = $this->getCurrentOrder()) {
                if ($currentOrder === 'relevance') {
                    try {
                        $searchTbl = $this->_collection->getSelect()->getPart('from')['search_result'] ?? null;
                        if ($searchTbl) {
                            $order = $this->_collection->getSelect()->getPart('order') ?: [];
                            $this->_collection->getSelect()->setPart(
                                'order',
                                array_merge([['search_result.score', 'DESC']], $order)
                            );
                        }
                    } catch (\Exception $e) {
                    }
                } else {
                    $this->_collection->addAttributeToSort(
                        $currentOrder,
                        $this->getCurrentDirection()
                    );
                }
            }
        }

        return $this;
    }

    /**
     * @override
     *
     * @return string
     */
    public function getCurrentDirection()
    {
        $dir = $this->_getData('_current_grid_direction');
        if ($dir) {
            return $dir;
        }

        $directions = ['asc', 'desc'];
        $customDir = \SM\Catalog\Helper\ProductList\Toolbar::getDirection();
        $order = $this->getCurrentOrder();
        $dir = $customDir[$order] ?? strtolower($this->toolbarMemorizer->getDirection());

        if (!$dir || !in_array($dir, $directions)) {
            $dir = $this->_direction;
        }

        if ($this->toolbarMemorizer->isMemorizingAllowed()) {
            $this->httpContext->setValue(
                \Magento\Catalog\Model\Product\ProductList\Toolbar::DIRECTION_PARAM_NAME,
                $dir,
                $this->_direction
            );
        }

        $this->setData('_current_grid_direction', $dir);

        return $dir;
    }

    /**
     * @override
     *
     * @return string
     */
    public function getCurrentOrder()
    {
        $order = $this->_getData('_current_grid_order');
        if ($order) {
            return $order;
        }

        $orders = $this->getAvailableOrders();
        $defaultOrder = $this->getOrderField();

        if (!isset($orders[$defaultOrder])) {
            $keys = array_keys($orders);
            $defaultOrder = $keys[0];
        }

        $order = $this->toolbarMemorizer->getOrder();
        if (!$order || !isset($orders[$order])) {
            $order = $defaultOrder;
        }

        if ($this->isOrderByPrice($order)) {
            $order = 'price';
        }

        if ($this->toolbarMemorizer->isMemorizingAllowed()) {
            $this->httpContext->setValue(
                \Magento\Catalog\Model\Product\ProductList\Toolbar::ORDER_PARAM_NAME,
                $order,
                $defaultOrder
            );
        }

        $this->setData('_current_grid_order', $order);

        return $order;
    }

    /**
     * @override
     *
     * @param string $order
     *
     * @return bool
     */
    public function isOrderCurrent($order)
    {
        $defaultOrder = $this->getOrderField();
        $orders = $this->getAvailableOrders();
        if (!isset($orders[$defaultOrder])) {
            $keys = array_keys($orders);
            $defaultOrder = $keys[0];
        }

        $currentOrder = $this->toolbarMemorizer->getOrder();
        if (!$currentOrder || !isset($orders[$currentOrder])) {
            $currentOrder = $defaultOrder;
        }

        return $currentOrder === $order;
    }

    /**
     * @param $order
     *
     * @return bool
     */
    public function isOrderByPrice($order)
    {
        $allow = [
            \SM\Catalog\Helper\ProductList\Toolbar::SORT_BY_PRICE_HIGH_TO_LOW,
            \SM\Catalog\Helper\ProductList\Toolbar::SORT_BY_PRICE_LOW_TO_HIGH
        ];

        if (in_array($order, $allow)) {
            return true;
        }

        return false;
    }
}
