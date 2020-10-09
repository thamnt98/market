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

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use SM\FileManagement\Api\Data\FileInterfaceFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use SM\FileManagement\Api\FileRepositoryInterface;
use SM\FileManagement\Model\ResourceModel\File as ResourceFile;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Store\Model\StoreManagerInterface;
use SM\FileManagement\Model\ResourceModel\File\CollectionFactory as FileCollectionFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use SM\FileManagement\Api\Data\FileSearchResultsInterfaceFactory;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;

/**
 * Class FileRepository
 *
 * @package SM\FileManagement\Model
 */
class FileRepository implements FileRepositoryInterface
{
    /**
     * @var FileSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var FileInterfaceFactory
     */
    protected $dataFileFactory;

    /**
     * @var JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var ResourceFile
     */
    protected $resource;

    /**
     * @var FileCollectionFactory
     */
    protected $fileCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @param ResourceFile $resource
     * @param FileFactory $fileFactory
     * @param FileInterfaceFactory $dataFileFactory
     * @param FileCollectionFactory $fileCollectionFactory
     * @param FileSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceFile $resource,
        FileFactory $fileFactory,
        FileInterfaceFactory $dataFileFactory,
        FileCollectionFactory $fileCollectionFactory,
        FileSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->fileFactory = $fileFactory;
        $this->fileCollectionFactory = $fileCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataFileFactory = $dataFileFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \SM\FileManagement\Api\Data\FileInterface $file
    ) {
        $fileData = $this->extensibleDataObjectConverter->toNestedArray(
            $file,
            [],
            \SM\FileManagement\Api\Data\FileInterface::class
        );

        $fileModel = $this->fileFactory->create()->setData($fileData);

        try {
            $this->resource->save($fileModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the file: %1',
                $exception->getMessage()
            ));
        }
        return $fileModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($fileId)
    {
        $file = $this->fileFactory->create();
        $this->resource->load($file, $fileId);
        if (!$file->getId()) {
            throw new NoSuchEntityException(__('File with id "%1" does not exist.', $fileId));
        }
        return $file->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->fileCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \SM\FileManagement\Api\Data\FileInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \SM\FileManagement\Api\Data\FileInterface $file
    ) {
        try {
            $fileModel = $this->fileFactory->create();
            $this->resource->load($fileModel, $file->getFileId());
            $this->resource->delete($fileModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the File: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($fileId)
    {
        return $this->delete($this->get($fileId));
    }
}
