<?php

/**
 * @category  SM
 * @package   SM_Label
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author    Logan Ta <phuongtt2@smartosc.com>
 *
 * @copyright 2020 Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

declare(strict_types=1);

namespace SM\Theme\Block\Page;

use Magento\Customer\Model\Context;
use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Http\Context as HttpContext;

/**
 * Class Loading
 * @package SM\Theme\Block\Page
 */
class Loading extends \Magento\Framework\View\Element\Template
{
    const CMS_HOME_PAGE = 'cms_index_index';
    /**
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * Loading constructor.
     * @param Template\Context $context
     * @param HttpContext $httpContext
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        HttpContext $httpContext,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->httpContext = $httpContext;
    }

    /**
     * Checking customer login status
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->httpContext->getValue(Context::CONTEXT_AUTH);
    }

    /**
     * @return Template
     */
    protected function _prepareLayout()
    {
        if ($this->isLoggedIn()) {
            $this->pageConfig->addBodyClass('page-loading');
        }

        return parent::_prepareLayout();
    }

    /**
     * @return bool
     */
    public function isHomepage()
    {
        if($this->getRequest()->getFullActionName() == self::CMS_HOME_PAGE){
           return true;
        }

        return false;
    }
}
