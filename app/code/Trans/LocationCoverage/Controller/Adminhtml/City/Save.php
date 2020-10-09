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

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Trans\LocationCoverage\Model\City
     */
    var $gridFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Trans\LocationCoverage\Model\City $gridFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Trans\LocationCoverage\Model\City $gridFactory
    ) {
        parent::__construct($context);
        $this->cityFactory = $gridFactory;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            $this->_redirect('locationcoverage/city/addrow');
            return;
        }
        try {
            $rowData = $this->cityFactory->create();
            $rowData->setData($data);
            if (isset($data['id'])) {
                $rowData->setCityId($data['id']);
            }
            $rowData->save();
            $this->messageManager->addSuccess(__('Row data has been successfully saved.'));
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $this->_redirect('locationcoverage/City/index');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Trans_LocationCoverage::save');
    }
}
