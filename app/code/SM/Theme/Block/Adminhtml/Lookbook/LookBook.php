<?php
/**
 * Class Lookbook
 * @package SM\Theme\Block\Adminhtml\Lookbook
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\Theme\Block\Adminhtml\Lookbook;

class LookBook extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Block constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_lookbook';
        $this->_blockGroup = 'MGS_Lookbook';
        $this->_headerText = __('Hot Spot');
        $this->_addButtonLabel = __('Add Hot Spot');
        parent::_construct();
    }
}
