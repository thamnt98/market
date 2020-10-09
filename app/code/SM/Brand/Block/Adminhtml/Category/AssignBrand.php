<?php

/**
 * @category SM
 * @package SM_Brand
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author      Chinhvd <chinhvd@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Brand\Block\Adminhtml\Category;

use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Block\Adminhtml\Category\Tab\Product;
use Magento\Framework\Registry;

class AssignBrand extends \Magento\Backend\Block\Template
{
    /**
     * @var Product
     */
    protected $blockGrid;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * AssignProducts constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->registry = $registry;
        parent::__construct($context, $data);
        $this->setTemplate('SM_Brand::category/edit/assign_brand.phtml');
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
                $this->blockGrid = $this->getLayout()->getBlock('category.form.assign_brand');
            } catch (\Exception $e) {
            }
        }

        return $this->blockGrid;
    }
}
