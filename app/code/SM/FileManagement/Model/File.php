<?php
/**
 * Class FileRepository
 * @package SM\FileManagement\Model
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\FileManagement\Model;

use Magento\Framework\Api\DataObjectHelper;
use SM\FileManagement\Api\Data\FileInterfaceFactory;
use SM\FileManagement\Api\Data\FileInterface;

/**
 * Class File
 *
 * @package SM\FileManagement\Model
 */
class File extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var FileInterfaceFactory
     */
    protected $fileDataFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var string
     */
    protected $_eventPrefix = 'sm_filemanagement_file';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param FileInterfaceFactory $fileDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \SM\FileManagement\Model\ResourceModel\File $resource
     * @param \SM\FileManagement\Model\ResourceModel\File\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        FileInterfaceFactory $fileDataFactory,
        DataObjectHelper $dataObjectHelper,
        \SM\FileManagement\Model\ResourceModel\File $resource,
        \SM\FileManagement\Model\ResourceModel\File\Collection $resourceCollection,
        array $data = []
    ) {
        $this->fileDataFactory = $fileDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve file model with file data
     * @return FileInterface
     */
    public function getDataModel()
    {
        $fileData = $this->getData();

        $fileDataObject = $this->fileDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $fileDataObject,
            $fileData,
            FileInterface::class
        );

        return $fileDataObject;
    }
}
