<?php

namespace SM\StoreLocator\Controller\Adminhtml\Location;

use Magento\Framework\Controller\ResultFactory;
use SM\StoreLocator\Controller\Adminhtml\AbstractLocationForm;

/**
 * Class ImportForm
 * @package SM\StoreLocator\Controller\Adminhtml\Location
 */
class ImportForm extends AbstractLocationForm
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
