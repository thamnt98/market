<?php
/**
 * Class Metadata
 * @package SM\FileManagement\Controller\Adminhtml\ContentType\File
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\Theme\Controller\Adminhtml\ContentType\File;

use Magento\Framework\Controller\ResultFactory;

class Metadata extends \Magento\Backend\App\AbstractAction
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'SM_FileManagement::top_level';

    /**
     * @var \SM\FileManagement\Model\ResourceModel\File\CollectionFactory
     */
    private $fileCollectionFactory;

    /**
     * DataProvider constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \SM\FileManagement\Model\ResourceModel\File\CollectionFactory $fileCollectionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \SM\FileManagement\Model\ResourceModel\File\CollectionFactory $fileCollectionFactory
    ) {
        parent::__construct($context);

        $this->fileCollectionFactory = $fileCollectionFactory;
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();
        try {
            $collection = $this->fileCollectionFactory->create();
            $blocks = $collection
                ->addFieldToSelect(['title'])
                ->addFieldToFilter('file_id', ['eq' => $params['file_id']])
                ->load();
            $result = $blocks->getFirstItem()->toArray();
        } catch (\Exception $e) {
            $result = [
                'error' => $e->getMessage(),
                'errorcode' => $e->getCode()
            ];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
