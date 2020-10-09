<?php
/**
 * Class Index
 * @package SM\Theme\Controller\Adminhtml\Lookbook
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\Theme\Controller\Adminhtml\Lookbook;

class Index extends \MGS\Lookbook\Controller\Adminhtml\Lookbook
{
    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Hot Spot'));
        $this->_view->renderLayout();
    }
}
