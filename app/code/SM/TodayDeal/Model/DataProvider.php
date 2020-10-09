<?php

/**
 * @category SM
 * @package SM_TodayDeal
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Chinhvd <chinhvd@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\TodayDeal\Model;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\UrlInterface;
use SM\TodayDeal\Model\ResourceModel\Post\CollectionFactory;

class DataProvider extends \Magento\Ui\DataProvider\ModifierPoolDataProvider
{
    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $status;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $postCollectionFactory
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $status
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param DataPersistorInterface $dataPersistor
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $postCollectionFactory,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $status,
        \Magento\Catalog\Helper\Image $imageHelper,
        DataPersistorInterface $dataPersistor,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $meta = [],
        array $data = []
    ) {
        $this->collection    = $postCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->storeManager  = $storeManager;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->status = $status;
        $this->imageHelper = $imageHelper;
    }

    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        $this->loadedData = array();
        /** @var $post \SM\TodayDeal\Model\Post */
        foreach ($items as $post) {
            $this->loadedData[$post->getId()] = $post->getData();
            if ($post->getCustomLayoutUpdateXml() || $post->getLayoutUpdateXml()) {
                //Deprecated layout update exists.
                $this->loadedData[$post->getId()]['layout_update_selected'] = '_existing_';
            }

            //Load thumbnail
            if ($post->getThumbnailName()) {
                $thumbnail['thumbnail'][0]['name'] = $post->getThumbnailName();
                $thumbnail['thumbnail'][0]['path'] = $post->getThumbnailPath();
                $thumbnail['thumbnail'][0]['url'] = $this->getMediaUrl() . $post->getThumbnailPath();
                $thumbnail['thumbnail'][0]['size'] = $post->getThumbnailSize();
                $fullData = $this->loadedData;
                $this->loadedData[$post->getId()] = array_merge($fullData[$post->getId()], $thumbnail);
            }

            $this->prepareImageData($post, 'mb_image', 'mb_image_path');
            $this->prepareVideoData($post, 'mb_video', 'mb_video_path');

            $this->loadedData[$post->getId()]['links']['products'] = [];
            $cnt = 1;

            foreach ($post->getRelatedProducts() as $product) {
                if ($product && $product->getId()) {
                    $this->loadedData[$post->getId()]['links']['products'][] = [
                        'id'        => $product->getId(),
                        'name'      => $product->getName(),
                        'status'    => $this->status->getOptionText($product->getStatus()),
                        'thumbnail' => $this->imageHelper->init($product, 'product_listing_thumbnail')->getUrl(),
                        'position'  => $cnt++,
                    ];
                }
            }

            foreach ($post->getRelatedIds() as $relatedId) {
                $this->loadedData[$post->getId()]['mb_related_campaigns'][] = $relatedId;
            }
        }

        $data = $this->dataPersistor->get('today_deals_post');
        if (!empty($data)) {
            $post = $this->collection->getNewEmptyItem();
            $post->setData($data);
            $this->loadedData[$post->getId()] = $post->getData();
            if ($post->getCustomLayoutUpdateXml() || $post->getLayoutUpdateXml()) {
                $this->loadedData[$post->getId()]['layout_update_selected'] = '_existing_';
            }
            $this->dataPersistor->clear('today_deals_post');
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
            ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * @param \SM\TodayDeal\Model\Post $post
     * @param string $name
     * @param string $path
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function prepareImageData($post, $name, $path)
    {
        $imageName = $post->getData($name);
        $imagePath = $post->getData($path);
        if ($imagePath && file_exists(UrlInterface::URL_TYPE_MEDIA . '/' . $imagePath)) {
            $this->loadedData[$post->getId()][$name] = [
                [
                    'name' => $imageName,
                    'path' => $imagePath,
                    'url'  => $this->getMediaUrl() . $imagePath,
                    'size' => filesize(UrlInterface::URL_TYPE_MEDIA . '/' . $imagePath),
                    'type' => 'image',
                ]
            ];
        } else {
            $this->loadedData[$post->getId()][$name] = null;
        }
    }

    /**
     * @param \SM\TodayDeal\Model\Post $post
     * @param string $name
     * @param string $path
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function prepareVideoData($post, $name, $path)
    {
        $videoName = $post->getData($name);
        $videoPath = $post->getData($path);
        if ($videoPath && file_exists(UrlInterface::URL_TYPE_MEDIA . '/' . $videoPath)) {
            $this->loadedData[$post->getId()]['file'] = [
                [
                    'name' => $videoName,
                    'path' => $videoPath,
                    'url'  => $this->getMediaUrl() . $videoPath,
                    'size' => filesize(UrlInterface::URL_TYPE_MEDIA . '/' . $videoPath)
                ]
            ];
        } else {
            $this->loadedData[$post->getId()]['file'] = null;
        }
    }
}
