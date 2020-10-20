<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Catalog
 *
 * Date: May, 14 2020
 * Time: 1:58 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Catalog\Block\Product;

class Delivery extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \SM\Catalog\Model\Source\Delivery\Method
     */
    protected $helper;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @var string
     */
    protected $_template = 'product/view/delivery.phtml';

    /**
     * Delivery constructor.
     *
     * @param \SM\Catalog\Helper\Delivery                      $helper
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array                                            $data
     */
    public function __construct(
        \SM\Catalog\Helper\Delivery $helper,
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
     * @return array
     */
    public function getMethods()
    {
        $product = $this->getProduct();

        try {
            return $this->helper->getDeliveryMethod($product);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return [];
        }
    }
}
