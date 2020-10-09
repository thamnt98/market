<?php

namespace SM\MobileApi\Model\Product;

/**
 * Class Image
 * @package SM\MobileApi\Model\Product
 */
class Image
{
    const IMAGE_360_PATH = 'mobile_api/product/image360';
    const EXTERNAL_VIDEO = 'external-video';
    const IMAGES_360 = 'images_360';
    const IMAGES_360_LABEL = 'Images 360';

    protected $_imageTypes = [ 'image', 'small_image', 'thumbnail' ];

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \SM\MobileApi\Model\Data\Catalog\Product\ImageFactory
     */
    protected $_imageFactory;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    protected $_appEmulation;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $_imageHelper;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $_directoryList;

    /**
     * @var \SM\MobileApi\Model\Data\Catalog\Product\ProductMediaFactory
     */
    protected $_productMediaFactory;

    /**
     * @var \MagicToolbox\Magic360\Model\ResourceModel\Gallery\CollectionFactory
     */
    protected $_image360Gallery;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlInterface;

    /**
     * Image constructor.
     * @param \SM\MobileApi\Model\Data\Catalog\Product\ImageFactory $imageFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Store\Model\App\Emulation $emulation
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \SM\MobileApi\Model\Data\Catalog\Product\ProductMediaFactory $productMediaFactory
     * @param \MagicToolbox\Magic360\Model\ResourceModel\Gallery\CollectionFactory $image360Gallery
     * @param \Magento\Framework\UrlInterface $urlInterface
     */
    public function __construct(
        \SM\MobileApi\Model\Data\Catalog\Product\ImageFactory $imageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Store\Model\App\Emulation $emulation,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \SM\MobileApi\Model\Data\Catalog\Product\ProductMediaFactory $productMediaFactory,
        \MagicToolbox\Magic360\Model\ResourceModel\Gallery\CollectionFactory $image360Gallery,
        \Magento\Framework\UrlInterface $urlInterface
    ) {
        $this->_imageFactory        = $imageFactory;
        $this->_storeManager        = $storeManager;
        $this->_appEmulation        = $emulation;
        $this->_imageHelper         = $imageHelper;
        $this->_directoryList       = $directoryList;
        $this->_productMediaFactory = $productMediaFactory;
        $this->_image360Gallery     = $image360Gallery;
        $this->urlInterface         = $urlInterface;
    }

    /**
     * Get default product image on listing page
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException|\Magento\Framework\Exception\FileSystemException
     */
    public function getMainImage($product)
    {
        if (!$product->getId()) {
            return '';
        }

        $imgType   = $this->_checkImageFileExist($product);
        $imgWidth  = 400;
        $imgHeight = 400;

        if ($product->getImage() == null || $product->getImage() == 'no_selection') {
            $storeId = $this->_storeManager->getStore()->getId();
            $this->_appEmulation->startEnvironmentEmulation($storeId, \Magento\Framework\App\Area::AREA_FRONTEND, true);
            $placeHolderImage = $this->_imageHelper->getDefaultPlaceholderUrl('image');
            $this->_appEmulation->stopEnvironmentEmulation();
            return $placeHolderImage;
        }

        $helper = $this->_imageHelper->init($product, $imgType);
        $helper->setImageFile($product->getData($imgType));
        $helper->resize($imgWidth, $imgHeight);

        return $helper->getUrl();
    }

    /**
     * Get Product's media infomations
     *
     * @param $product
     *
     * @return \SM\MobileApi\Model\Data\Catalog\Product\ProductMedia
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMediaUrlsData($product)
    {
        $media = $this->_productMediaFactory->create();
        /** @var \Magento\Catalog\Model\Product $product */
        foreach ($this->_imageTypes as $type) {
            $_image = $this->_imageFactory->create();
            if ($product->getData($type)) {
                $_helper = $this->_imageHelper->init($product, '');
                $_image->setUrl($_helper->setImageFile($product->getData($type))->getUrl());
                $_image->setLabel($product->getName());
            } else {
                $_image->setUrl('');
                $_image->setLabel('');
            }
            $media->setData($type, $_image);
        }

        $media->setImage360($this->getImage360($product->getId()));

        return $media;
    }

    /**
     * Get product's gallery
     *
     * @param $product
     *
     * @param string $ignore
     * @return \SM\MobileApi\Api\Data\Catalog\Product\ImageInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getGalleryInfo($product, $ignore = '')
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $images = $product->getMediaGalleryEntries();
        $data = [];

        if (!$images) {
            return $data;
        }

        $position = 0;
        $_ignoreName = $this->_getImageFileName($ignore);
        $images360 = $this->getImage360($product->getId());
        $storeId = $this->_storeManager->getStore()->getId();

        $this->_appEmulation->startEnvironmentEmulation($storeId, \Magento\Framework\App\Area::AREA_FRONTEND, true);
        foreach ($images as $img) {
            $_helper = $this->_imageHelper->init($product, '');
            $_imageName = $this->_getImageFileName($img->getFile());

            if ($img->getDisabledDefault() || $img->getDisabled()) {
                continue;
            }

            if ($img->getFile()) {
                $_image = $this->_imageFactory->create();
                $_image->setUrl($_helper->setImageFile($img->getFile())->getUrl());
                $_image->setLabel($img->getLabel() ?: $product->getName());

                if ($img->getMediaType() == self::EXTERNAL_VIDEO) {
                    $_image->setType(self::EXTERNAL_VIDEO);
                    $_image->setVideoUrl($img->getExtensionAttributes()->getVideoContent()->getVideoUrl());
                } else {
                    $_image->setType($img->getMediaType());
                }

                $data[$position] = $_image;
            }
            //Push thumbnail image in top of array
            if ($_ignoreName == $_imageName && $position != 0) {
                $change = $data[0];
                $data[0] = $data[$position];
                $data[$position] = $change;
            }
            $position++;
        }
        $this->_appEmulation->stopEnvironmentEmulation();

        //Add Image 360 to gallery
        if (!empty($images360)) {
            $_image = $this->_imageFactory->create();
            $thumbnailImage = $this->_imageHelper->init($product, 'product_thumbnail_image');
            $_image->setUrl($thumbnailImage->getUrl());
            $_image->setLabel(self::IMAGES_360_LABEL);
            $_image->setType(self::IMAGES_360);
            $_image->set360Url($this->urlInterface->getBaseUrl() . self::IMAGE_360_PATH . '?product_id=' . $product->getId());
            $data[] = $_image;
        }
        return $data;
    }

    /**
     * Get list image
     *
     * @param int $productId
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getImage360($productId)
    {
        $collection = $this->_image360Gallery->create();
        $collection->addFieldToFilter('product_id', $productId)
            ->addFieldToSelect('file')
            ->setOrder('position', 'ASC');

        $result=[];
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        foreach ($collection->getData() as $key => $item) {
            $result[] = $mediaUrl . 'magic360' . $item['file'];
        }
        return $result;
    }

    /**
     * Get image file name from Url
     *
     * @param $url
     *
     * @return string
     */
    protected function _getImageFileName($url)
    {
        return basename($url);
    }

    /**
     * Check to get image type which available
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function _checkImageFileExist($product)
    {
        $mediaDir   = $this->_directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $imageTypes = $this->_imageTypes;

        /**
         * Append default image type for listing page to first, so it allways check first
         */

        foreach ($imageTypes as $imageType) {
            if (! $imageType) {
                continue;
            }

            $filePath = $mediaDir . DIRECTORY_SEPARATOR . 'catalog' . DIRECTORY_SEPARATOR . 'product' . $product->getData($imageType);

            if (file_exists($filePath) && is_file($filePath)) {
                return $imageType;
            }
        }

        return 'image';
    }
}
