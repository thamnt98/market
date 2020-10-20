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

namespace SM\TodayDeal\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;
use Magento\Store\Model\StoreManagerInterface;


class HelperImage extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var AdapterFactory
     */
    private $imageFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;


    /**
     * SubCategories constructor.
     * @param StoreManagerInterface $storeManager
     * @param Context $context
     * @param Filesystem $filesystem
     * @param AdapterFactory $imageFactory
     * @param Data $helper
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Context $context,
        Filesystem $filesystem,
        AdapterFactory $imageFactory
    ) {
        $this->storeManager = $storeManager;
        $this->filesystem   = $filesystem;
        $this->imageFactory = $imageFactory;
        parent::__construct($context);
    }

    /**
     * @param $image
     * @param $path
     * @param null $width
     * @param null $height
     * @return bool|string
     * @throws \Exception
     */
    public function getImageResize($image, $path, $width = null, $height = null)
    {
        $absolutePath = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
            ->getAbsolutePath($path);
        if (!file_exists($absolutePath)) {
            return false;
        }
        $resizePath = 'resized/' . $width . '/';
        $imageResized = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
                ->getAbsolutePath($resizePath) . $image;
        if (!file_exists($imageResized)) { // Only resize image if not already exists.
            //create image factory...
            $imageResize = $this->imageFactory->create();
            $imageResize->open($absolutePath);
            $imageResize->constrainOnly(true);
            $imageResize->keepTransparency(true);
            $imageResize->keepFrame(false);
            $imageResize->keepAspectRatio(true);
            $imageResize->resize($width, $height);
            //destination folder
            $destination = $imageResized ;
            //save image
            $imageResize->save($destination);
        }
        $store = $this->storeManager->getStore();
        return $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $resizePath . $image;
    }
}
