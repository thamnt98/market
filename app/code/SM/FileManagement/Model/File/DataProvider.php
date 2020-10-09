<?php
/**
 * Class DataProvider
 * @package SM\FileManagement\Model\File
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\FileManagement\Model\File;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Store\Model\StoreManagerInterface;
use SM\FileManagement\Model\ResourceModel\File\CollectionFactory;

/**
 * Class DataProvider
 *
 * @package SM\FileManagement\Model\File
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var \SM\FileManagement\Model\ResourceModel\File\Collection
     */
    protected $collection;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param StoreManagerInterface $storeManager
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        StoreManagerInterface $storeManager,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->storeManager = $storeManager;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();
        foreach ($items as $model) {
            $this->loadedData[$model->getId()] = $model->getData();
            if ($model->getFileName()) {
                $file['file'][0]['name'] = $model->getFileName();
                $file['file'][0]['file'] = $model->getFileName();
                $file['file'][0]['path'] = $model->getFilePath();
                $file['file'][0]['url'] = $this->getMediaUrl() . $model->getFilePath();
                $file['file'][0]['size'] = $model->getFileSize();
                $fullData = $this->loadedData;
                $this->loadedData[$model->getId()] = array_merge($fullData[$model->getId()], $file);
            }

            if ($model->getThumbnailName()) {
                $thumbnail['thumbnail'][0]['name'] = $model->getThumbnailName();
                $thumbnail['thumbnail'][0]['file'] = $model->getThumbnailName();
                $thumbnail['thumbnail'][0]['path'] = $model->getThumbnailPath();
                $thumbnail['thumbnail'][0]['url'] = $this->getMediaUrl() . $model->getThumbnailPath();
                $thumbnail['thumbnail'][0]['size'] = $model->getThumbnailSize();
                $fullData = $this->loadedData;
                $this->loadedData[$model->getId()] = array_merge($fullData[$model->getId()], $thumbnail);
            }
        }

        $data = $this->dataPersistor->get('sm_filemanagement_file');

        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getId()] = $model->getData();
            $this->dataPersistor->clear('sm_filemanagement_file');
        }

        return $this->loadedData;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getMediaUrl()
    {
        return $this->storeManager->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
}
