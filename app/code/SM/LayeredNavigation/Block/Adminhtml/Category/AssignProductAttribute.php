<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_LayeredNavigation
 *
 * Date: April, 27 2020
 * Time: 2:44 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\LayeredNavigation\Block\Adminhtml\Category;

class AssignProductAttribute extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Catalog\Block\Adminhtml\Category\Tab\Product
     */
    protected $blockGrid;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * AssignProducts constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->jsonEncoder = $jsonEncoder;
        parent::__construct($context, $data);

        $this->setTemplate('SM_LayeredNavigation::category/edit/assign_attribute.phtml');
    }

    /**
     * Retrieve instance of grid block
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            try {
                $this->blockGrid = $this->getLayout()->getBlock('category.form.assign_attribute');
            } catch (\Exception $e) {
            }
        }

        return $this->blockGrid;
    }
}
