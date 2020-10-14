<?php
/**
 * Class Save
 *
 * PHP version 7
 *
 * @category Trans
 * @package  Trans_Brand
 * @author   J.P <jaka.pondan@transdigital.co.id>
 */
namespace Trans\Brand\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\TestFramework\ErrorLog\Logger;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Filesystem\Io\File;

/**
 * Class Save
 *
 * @category Trans
 * @package  Trans_Brand
 * @author   J.P <jaka.pondan@transdigital.co.id>
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * UploaderFactory
     *
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * JsHelper
     *
     * @var \Magento\Backend\Helper\Js
     */
    protected $jsHelper;

    /**
     * BrandFactory
     *
     * @var \Trans\Brand\Model\BrandFactory
     */
    protected $brandFactory;

    /**
     * AdapterInterface
     *
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * ResourceConnection
     *
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * DirectoryList
     *
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * SessionModel
     *
     * @var \Magento\Backend\Model\Session
     */
    protected $sessionModel;

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
     * Save constructor.
     *
     * @param Action\Context                                  $context           context
     * @param UploaderFactory                                 $uploaderFactory   uploaderFactory
     * @param \Magento\Backend\Helper\Js                      $jsHelper          jsHelper
     * @param \Trans\Brand\Model\BrandFactory            $brandFactory      brandFactory
     * @param \Magento\Framework\App\ResourceConnection       $resource          resource
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList     directoryList
     * @param \Magento\Backend\Model\Session                  $sessionModel      sessionModel
     * @param \Magento\UrlRewrite\Model\UrlRewriteFactory     $urlRewriteFactory urlRewriteFactory
     * @param \Magento\Framework\Filesystem\Io\File           $file              file
     * @param \Magento\Store\Model\StoreManagerInterface      $storeManager      storeManager
     */
    public function __construct(
        Action\Context $context,
        UploaderFactory $uploaderFactory,
        \Magento\Backend\Helper\Js $jsHelper,
        \Trans\Brand\Model\BrandFactory $brandFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Backend\Model\Session $sessionModel,
        \Magento\UrlRewrite\Model\UrlRewriteFactory $urlRewriteFactory,
        File $file,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->uploaderFactory = $uploaderFactory;
        $this->jsHelper = $jsHelper;
        $this->brandFactory = $brandFactory;
        $this->connection = $resource->getConnection();
        $this->resource = $resource;
        $this->directoryList = $directoryList;
        $this->sessionModel = $sessionModel;
        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->file = $file;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Trans_Brand::save');
    }

    /**
     * Get store identifier
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        $files = $this->getRequest()->getFiles();

        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data) {
            $model = $modelProduct = $this->brandFactory->create();

            $model->setData($data);

            if (isset($data['brand_id'])) {
                $model->setBrandId($data['brand_id']);
                $modelProduct->setBrandId($data['brand_id']);
            }

            try {
                if (!empty($files['image']['name'])) {
                    $uploader = $this->uploaderFactory->create(['fileId' => 'image']);
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                    $uploader->setFilesDispersion(false);
                    $uploader->setFilenamesCaseSensitivity(false);
                    $uploader->setAllowRenameFiles(true);
                    $path = $this->directoryList->getPath(
                        \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
                    ).'/trans/brand';
                    $result = $uploader->save($path);
                    $model->setImage('trans/brand/'.$result['file']);
                } else {
                    $model->setImage($data['image']['value']);
                }

                if (!empty($files['banner_image']['name'])) {
                    if (isset($data['banner_image']['delete'])) {
                        $this->file->rm(
                            $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
                            .'/'
                            .$data['banner_image']['value']
                        );
                        $model->setData('banner_image', '');
                    } else {
                        $model->unsetData('banner_image');
                    }

                    $uploader = $this->uploaderFactory->create(['fileId' => 'banner_image']);
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                    $uploader->setFilesDispersion(false);
                    $uploader->setFilenamesCaseSensitivity(false);
                    $uploader->setAllowRenameFiles(true);
                    $path = $this->directoryList
                            ->getPath(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA).'/trans/brand/banner';
                    $result = $uploader->save($path);
                    $model->setBannerImage('trans/brand/banner/'.$result['file']);
                } else {
                    if (isset($data['banner_image']['value']) && !empty($data['banner_image']['value'])) {
                        if (isset($data['banner_image']['delete'])) {
                            $this->file->rm(
                                $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
                                .'/'
                                .$data['banner_image']['value']
                            );
                            $model->setData('banner_image', '');
                        } else {
                            $model->unsetData('banner_image');
                        }
                    }
                }

                $model->save();

                $urlRewriteModel = $this->urlRewriteFactory->create();
                $urlRewriteData = $urlRewriteModel->getCollection()
                    ->addFieldToFilter(
                        'target_path',
                        'brands/index/index/id/'.$model->getId()
                    );
                
                foreach ($urlRewriteData->getItems() as $rewrite) {
                    $this->deleteItem($rewrite);
                }

                if (!empty($data['url_key'])) {
                    try {
                        /* set current store id */
                        $urlRewriteModel->setStoreId($this->getStoreId());
                        /* this url is not created by system so set as 0 */
                        $urlRewriteModel->setIsSystem(0);
                        /* unique identifier - set random unique value to id path */
                        $urlRewriteModel->setIdPath(rand(1, 100000));

                        $urlRewriteModel->setTargetPath('brands/index/index/id/'.$model->getId());
                        /* set requested path which you want to create */
                        $urlRewriteModel->setRequestPath($data['url_key']);
                        /* save URL rewrite rule */
                        $urlRewriteModel->save();
                    } catch (\Exception $e) {
                        $this->messageManager->addException(
                            $e,
                            __('Something went wrong while saving the brand. Make sure you have entered the unique URL Key.')
                        );
                    }
                }
                
                if (isset($data['products'])) {
                    $this->saveProducts($modelProduct, $data['products']);
                }
                
                if (!$this->messageManager->hasMessages()) {
                    $this->messageManager->addSuccess(
                        __('Brand has been saved successfully.')
                    );
                }
                
                $this->sessionModel->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        ['brand_id' => $model->getId(), '_current' => true]
                    );
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                if ($e->getCode() != \Magento\Framework\File\Uploader::TMP_NAME_EMPTY) {
                    $this->messageManager->addException(
                        $e,
                        __('Please insert Image of types jpg, jpeg, gif, png')
                    );
                } else {
                    $this->messageManager->addException(
                        $e,
                        __('Something went wrong while saving the brand.')
                    );
                }
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath(
                '*/*/edit',
                ['brand_id' => $this->getRequest()->getParam('brand_id')]
            );
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Save products
     *
     * @param Trans\Brand\Model\BrandFactory $model         model
     * @param mixed                               $brandProducts brandProducts
     *
     * @return void
     */
    public function saveProducts($model, $brandProducts)
    {
        $productIds = $this->jsHelper
            ->decodeGridSerializedInput($brandProducts);

        try {
            $oldProducts = (array) $model->getProducts($model);
            $newProducts = (array) $productIds;

            $tableName = $this->resource->getTableName(
                \Trans\Brand\Model\ResourceModel\Brand::TBL_BRAND_PRODUCTS
            );

            $deletedProductsArray = array_diff($oldProducts, $newProducts);
            
            $productsInDb=array_intersect($oldProducts, $newProducts);
            
            foreach ($productsInDb as $key => $value) {
                $pos = array_search($value, $newProducts);
                unset($newProducts[$pos]);
            }

            if (!empty($deletedProductsArray)) {
                $where = [
                    'brand_id = ?' => (int)$model->getId(),
                    'product_id IN (?)' => $deletedProductsArray
                ];
                $this->connection->delete($tableName, $where);
            }

            if ($newProducts) {
                $data = [];
                foreach ($newProducts as $productId) {
                    $data[] = [
                        'brand_id' => (int)$model->getId(),
                        'product_id' => (int)$productId
                    ];
                }

                $this->connection->insertMultiple($tableName, $data);
            }
        } catch (\Exception $e) {
            $this->messageManager->addException(
                $e,
                __('Something went wrong while saving the contact.')
            );
        }
    }

    /**
     * Delete record of given item
     *
     * @param \Magento\UrlRewrite\Model\UrlRewriteFactory $item item
     *
     * @return void
     */
    public function deleteItem($item)
    {
        $item->delete();
    }
}
