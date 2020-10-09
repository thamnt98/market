<?php

/**
 * @category SM
 * @package SM_Brand
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author      Chinhvd <chinhvd@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Brand\Controller\Adminhtml\Category;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterfaceAlias;

class Grid extends \Magento\Catalog\Controller\Adminhtml\Category\Grid implements HttpPostActionInterfaceAlias
{
    /**
     * @var string
     */
    protected $blockClass = \SM\Brand\Block\Adminhtml\Category\Tab\BrandList\Grid::class;

    /**
     * @var string
     */
    protected $blockName = 'grid';

    /**
     * Grid Action
     *
     * Display list of products related to current category
     *
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\Controller\Result\Raw
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        if (!$this->blockClass || !$this->blockName) {
            throw new \Magento\Framework\Exception\NotFoundException(__('Page not found.'));
        }

        $category = $this->_initCategory(true);
        if (!$category) {
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('catalog/*/', ['_current' => true, 'id' => null]);
        }

        /** @var \SM\Brand\Block\Adminhtml\Category\Tab\BrandList\Grid $block */
        $block = $this->layoutFactory->create()->createBlock(
            $this->blockClass,
            $this->blockName
        );

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents(
            $block->toHtml()
        );
    }
}
