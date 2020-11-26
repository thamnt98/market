<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Catalog
 *
 * Date: June, 01 2020
 * Time: 2:12 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Catalog\Block\Product\ProductList\Item\Details;

class ChildrenLabel extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \SM\Catalog\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollFact;


    public function __construct(
        \SM\Catalog\Helper\Data $helper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollFact,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
        $this->productCollFact = $productCollFact;
    }

    /**
     * @return \Magento\Catalog\Model\Product|null
     */
    public function getProduct()
    {
        return $this->getData('product');
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return bool
     */
    public function isValidation($product = null)
    {
        if (!$product) {
            $product = $this->getProduct();
        }

        $typeValid = [
            \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE,
            \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE,
            \Magento\Bundle\Model\Product\Type::TYPE_CODE
        ];

        if (!$product ||
            !in_array($product->getTypeId(), $typeValid)
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param null $product
     *
     * @return int
     */
    public function countChildren($product = null)
    {
        if (!$product) {
            $product = $this->getProduct();
        }

        if (!$this->isValidation($product)) {
            return 0;
        }

        $result = 0;
        $ids = $product->getTypeInstance()->getChildrenIds($product->getId());
        $ids = $this->mergeArray($ids);

        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $coll */
        $coll = $this->productCollFact->create();
        $children = $coll->addFieldToFilter('entity_id', $ids);

        /** @var \Magento\Catalog\Model\Product $child */
        foreach ($children->getItems() as $child) {
            if (!$child->isSaleable()) {
                continue;
            }

            ++$result;
        }

        return $result;
    }

    protected function mergeArray($children)
    {
        $result = [];
        if (!is_array($children)) {
            $result[] = $children;
        } else {
            foreach ($children as $child) {
                if (is_array($child)) {
                    $result = array_merge($result, $this->mergeArray($child));
                } else {
                    $result[] = $child;
                }
            }
        }

        return array_unique($result);
    }
}
