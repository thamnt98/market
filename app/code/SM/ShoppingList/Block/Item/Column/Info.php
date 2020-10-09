<?php
/**
 * @category Magento
 * @package SM\ShoppingList\Block\Item\Column
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\ShoppingList\Block\Item\Column;

use Magento\Catalog\Block\Product\ListProduct;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use SM\Label\Model\LabelViewer;
use SM\ShoppingList\Block\Item\Column;
use Amasty\Label\Model\AbstractLabels;

/**
 * Class Info
 * @package SM\ShoppingList\Block\Item\Column
 */
class Info extends Column
{
    /**
     * @var ListProduct
     */
    protected $listProduct;

    /**
     * @return \Magento\Catalog\Model\Product
     */

    public function __construct(
        Template\Context $context,
        LabelViewer $helper,
        Registry $registry,
        ListProduct $listProduct,
        array $data = []
    ) {
        parent::__construct($context, $helper, $registry, $data);
        $this->listProduct = $listProduct;
    }

    public function getProductLabel()
    {
        return $this->product;
    }

    /**
     * @param $product
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getLabel($product)
    {
        $result = '';
        if ($product->getId()) {
            $result = $this->helper->renderProductLabel($product, AbstractLabels::CATEGORY_MODE, false);
        }

        return $result;
    }
    public function getProductPrice()
    {
        return $this->listProduct->getProductPrice($this->product);
    }
}
