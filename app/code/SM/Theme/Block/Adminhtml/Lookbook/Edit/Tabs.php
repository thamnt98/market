<?php
/**
 * Class Tabs
 * @package SM\Theme\Block\Adminhtml\Lookbook\Edit
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\Theme\Block\Adminhtml\Lookbook\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('lookbook_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Hot Spot Information'));
    }

    /**
     * @return \Magento\Backend\Block\Widget\Tabs|\Magento\Framework\View\Element\AbstractBlock
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'main_section',
            [
                'label' => __('General Information'),
                'content' => $this->getLayout()->createBlock('SM\Theme\Block\Adminhtml\Lookbook\Edit\Tab\Main')->toHtml(),
            ]
        );

        return parent::_beforeToHtml();
    }
}
