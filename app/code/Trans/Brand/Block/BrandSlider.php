<?php
/**
 * Class BrandSlider
 *
 * PHP version 7
 *
 * @category Trans
 * @package  Trans_Brand
 * @author   J.P <jaka.pondan@transdigital.co.id>
 */
namespace Trans\Brand\Block;

use Trans\Brand\Model\Brand;
use Magento\Framework\View\Element\Template;
use Trans\Brand\Model\ResourceModel\Brand\CollectionFactory;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Filesystem\Io\File;

/**
 * Class BrandSlider
 *
 * @category Trans
 * @package  Trans_Brand
 * @author   J.P <jaka.pondan@transdigital.co.id>
 */
class BrandSlider extends Template implements \Magento\Widget\Block\BlockInterface
{
    /**
     * StoreManager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * BrandCollectionFactory
     *
     * @var \Trans\Brand\Model\ResourceModel\Brand\CollectionFactory
     */
    protected $brandCollectionFactory;

    /**
     * Filesystem
     *
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * ImageFactory
     *
     * @var \Magento\Framework\Image\AdapterFactory
     */
    protected $imageFactory;

    /**
     * UrlRewriteFactory
     *
     * @var \Magento\UrlRewrite\Model\UrlRewriteFactory
     */
    protected $urlRewriteFactory;

    /**
     * FileSystem
     *
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $file;

    /**
     * BrandSlider constructor.
     *
     * @param Context                                     $context                context
     * @param CollectionFactory                           $brandCollectionFactory brandFactory
     * @param \Magento\Framework\Filesystem               $filesystem             filesystem
     * @param \Magento\Framework\Image\AdapterFactory     $imageFactory           imageFactory
     * @param \Magento\UrlRewrite\Model\UrlRewriteFactory $urlRewriteFactory      urlRewriteFactory
     * @param \Magento\Framework\Filesystem\Io\File       $file                   file
     * @param array                                       $data                   data
     */
    public function __construct(
        Context $context,
        CollectionFactory $brandCollectionFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\UrlRewrite\Model\UrlRewriteFactory $urlRewriteFactory,
        File $file,
        array $data = []
    ) {
        parent::__construct($context, $data);
        
        $this->storeManager = $context->getStoreManager();
        $this->brandCollectionFactory = $brandCollectionFactory;
        $this->filesystem = $filesystem;
        $this->imageFactory = $imageFactory;
        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->file = $file;
    }

    /**
     * Initialize Block BrandSlider
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate("Trans_Brand::brand_slider.phtml");
    }

    /**
     * Return Brand Collection
     *
     * @return mixed
     */
    public function getBrandCollection()
    {
        $brandCollection = $this->brandCollectionFactory->create()
            ->addFieldToFilter('status', Brand::STATUS_ENABLED)
            ->setOrder('position', 'DESC')
            ->setOrder('update_time', 'DESC');
        
        return $brandCollection;
    }

    /**
     * Return Brand Image Full Url
     *
     * @param Brand $brand brand
     *
     * @return string
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBrandImageUrl(Brand $brand)
    {
        return $this->storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        ).$brand->getImage();
    }

    /**
     * Return Brand Url By id
     *
     * @param int    $brandId  brandId
     * @param string $brandUrl brandUrl
     *
     * @return string
     */
    public function getBrandUrl($brandId, $brandUrl = '')
    {
        $urlRewriteModel = $this->urlRewriteFactory->create();
        $urlRewriteData = $urlRewriteModel->getCollection()
            ->addFieldToFilter('request_path', $brandUrl);
        if (!empty($urlRewriteData->getData())) {
            $url=$this->getUrl($urlRewriteData->getData()[0]['request_path']);
        } else {
            $url=$this->getUrl('brands/index', ['id'=>$brandId]);
        }

        return $url;
    }

    /**
     * Return resized brand image url
     *
     * @param string   $image  Imagepath
     * @param int|null $width  resizeImageWidth
     * @param int|null $height resizeImageHeight
     *
     * @return string
     */
    public function getResizeUrl($image, $width = null, $height = null)
    {
        $absolutePath = $this->filesystem
            ->getDirectoryRead(
                \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
            )->getAbsolutePath($image);

        if (!$this->file->fileExists($absolutePath)) {
            return false;
        }

        $resizedImage = str_replace("trans/brand/", "", $image);

        $imageResized = $this->filesystem
            ->getDirectoryRead(
                \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
            )->getAbsolutePath('trans/brand/resized/'.$width.'/').$resizedImage;

        if (!$this->file->fileExists($imageResized)) {
            $imageResize = $this->imageFactory->create();
            $imageResize->open($absolutePath);
            $imageResize->constrainOnly(true);
            $imageResize->keepTransparency(true);
            $imageResize->keepFrame(true);
            $imageResize->keepAspectRatio(true);
            $imageResize->backgroundColor([255,255,255]);
            $imageResize->resize($width, $height);
            //destination folder
            $destination = $imageResized ;
            //save image
            $imageResize->save($destination);
        }

        $resizedURL = $this->storeManager->getStore()
            ->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ).'trans/brand/resized/'.$width.'/'.$resizedImage;
        return $resizedURL;
    }
}
