<?php
/**
 * @category Trans
 * @package  Trans_LocationCoverage
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\LocationCoverage\Controller\Adminhtml\City;

class Index extends \Magento\Backend\App\Action
{
	/**
	 * @var \Magento\Framework\View\Result\PageFactory
	 */
	private $resultPageFactory;

	/**
	 * @param \Magento\Backend\App\Action\Context $context
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 */
	public function __construct(
	    \Magento\Backend\App\Action\Context $context,
	    \Magento\Framework\View\Result\PageFactory $resultPageFactory
	) {
	    parent::__construct($context);
	    $this->resultPageFactory = $resultPageFactory;
	}

	/**
	 * Mapped List page.
	 *
	 * @return \Magento\Backend\Model\View\Result\Page
	 */
	public function execute()
	{
	    $resultPage = $this->resultPageFactory->create();
	    $resultPage->setActiveMenu('Trans_LocationCoverage::grid_list');
	    $resultPage->getConfig()->getTitle()->prepend(__('Location Coverage - City'));
	    return $resultPage;
	}

	/**
	 * Check Order Import Permission.
	 *
	 * @return bool
	 */
	protected function _isAllowed()
	{
	    return $this->_authorization->isAllowed('Trans_LocationCoverage::grid_list');
	}
}
