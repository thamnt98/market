<?php

/**
 * @category SM
 * @package SM_Theme
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Chinhvd <chinhvd@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Theme\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;
use Magento\Store\Model\StoreManagerInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const PATH = 'media/resized/';

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
     * @param null $width
     * @param null $height
     * @return bool|string
     * @throws \Exception
     */
    public function getImageResize($image, $width = null, $height = null)
    {
        $store = $this->storeManager->getStore()->getBaseUrl();
        $img = str_replace($store, '', $image);
        if (strpos($img, \Magento\Framework\App\Filesystem\DirectoryList::PUB) !== false) {
            $absolutePath = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::ROOT)
                    ->getAbsolutePath() . $img;
        } else {
            $absolutePath = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::PUB)
                    ->getAbsolutePath() . $img;
        }
        if (!file_exists($absolutePath)) {
            return $image;
        }
        $resizePath = self::PATH . $width . '/';
        $imageResized = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::PUB)
                ->getAbsolutePath($resizePath) . $img;
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
            $destination = $imageResized;
            //save image
            $imageResize->save($destination);
        }
        return $store . $resizePath . $img;
    }
}
