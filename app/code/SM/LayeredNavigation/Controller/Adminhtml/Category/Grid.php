<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_LayeredNavigation
 *
 * Date: April, 27 2020
 * Time: 3:59 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\LayeredNavigation\Controller\Adminhtml\Category;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterfaceAlias;

class Grid extends \Magento\Catalog\Controller\Adminhtml\Category\Grid implements HttpPostActionInterfaceAlias
{
    /**
     * @var string
     */
    protected $blockClass = \SM\LayeredNavigation\Block\Adminhtml\Category\Tab\FilterList\Grid::class;

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

        /** @var \SM\LayeredNavigation\Block\Adminhtml\Category\Tab\FilterList\Grid $block */
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
