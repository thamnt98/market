<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Catalog
 *
 * Date: May, 14 2020
 * Time: 2:38 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Catalog\Block\Product;

class StorePickup extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @var string
     */
    protected $_template = 'product/view/store_pickup.phtml';

    /**
     * @var \SM\Catalog\Helper\StorePickup
     */
    protected $helper;

    /**
     * Delivery constructor.
     *
     * @param \SM\Catalog\Helper\StorePickup                   $helper
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array                                            $data
     */
    public function __construct(
        \SM\Catalog\Helper\StorePickup $helper,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param $product
     *
     * @return $this
     */
    public function setProduct($product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return array
     */
    public function getList($product)
    {
        try {
            $sourceListData = $this->helper->getListDataBySourceCode(
                $this->helper->getSourceCodesBySKU($product->getSku())
            );
            $sourceDataSortUpdated = $this->helper->calculateDistanceAndSortUpdated($sourceListData);
            return $this->helper->convertSourcesListSorted($sourceListData, $sourceDataSortUpdated);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @return string
     */
    public function getStoreTimeAvailable()
    {
        return $this->helper->getStoreTimeAvailable();
    }
}
