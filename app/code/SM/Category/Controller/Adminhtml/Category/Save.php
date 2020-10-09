<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Category
 *
 * Date: May, 06 2020
 * Time: 6:18 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Category\Controller\Adminhtml\Category;

class Save extends \Magento\Catalog\Controller\Adminhtml\Category\Save
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Psr\Log\LoggerInterface|null
     */
    protected $logger;
    /**
     * @var Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    private $categoryCollection;

    /**
     * Save constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Eav\Model\Config|null $eavConfig
     * @param \Psr\Log\LoggerInterface|null $logger
     * @param Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Eav\Model\Config $eavConfig = null,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection
    ) {
        parent::__construct(
            $context,
            $resultRawFactory,
            $resultJsonFactory,
            $layoutFactory,
            $dateFilter,
            $storeManager,
            $eavConfig,
            $logger
        );

        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->categoryCollection = $categoryCollection;
    }

    /**
     * Function execute
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $category = $this->_initCategory();

        if (!$category) {
            return $resultRedirect->setPath('catalog/*/', ['_current' => true, 'id' => null]);
        }

        $categoryPostData = $this->getRequest()->getPostValue();

        //Set Default Color White
        $categoryPostData['main_category_color'] = !empty($categoryPostData['main_category_color']) ?
            $categoryPostData['main_category_color'] : "#ffffff";

        $categoryPostData['sub_category_color'] = !empty($categoryPostData['sub_category_color'])  ?
            $categoryPostData['sub_category_color'] : "#ffffff";

        $categoryPostData['favorite_brand_color'] = !empty($categoryPostData['favorite_brand_color']) ?
            $categoryPostData['favorite_brand_color'] : "#ffffff";

        $categoryPostData['product_color'] = !empty($categoryPostData['product_color']) ?
            $categoryPostData['product_color'] : "#ffffff";

        $isNewCategory = !isset($categoryPostData['entity_id']);
        $categoryPostData = $this->stringToBoolConverting($categoryPostData);
        $categoryPostData = $this->imagePreprocessing($categoryPostData);
        $categoryPostData = $this->dateTimePreprocessing($category, $categoryPostData);
        $storeId = isset($categoryPostData['store_id']) ? $categoryPostData['store_id'] : null;
        $store = $this->storeManager->getStore($storeId);
        $this->storeManager->setCurrentStore($store->getCode());
        $parentId = isset($categoryPostData['parent']) ? $categoryPostData['parent'] : null;
        if ($categoryPostData) {
            $category->addData($categoryPostData);
            if ($parentId) {
                $category->setParentId($parentId);
            }
            if ($isNewCategory) {
                $parentCategory = $this->getParentCategory($parentId, $storeId);
                $category->setPath($parentCategory->getPath());
                $category->setParentId($parentCategory->getId());
                $category->setLevel(null);
            }

            /**
             * Process "Use Config Settings" checkboxes
             */

            $useConfig = [];
            if (isset($categoryPostData['use_config']) && !empty($categoryPostData['use_config'])) {
                foreach ($categoryPostData['use_config'] as $attributeCode => $attributeValue) {
                    if ($attributeValue) {
                        $useConfig[] = $attributeCode;
                        $category->setData($attributeCode, null);
                    }
                }
            }

            $category->setAttributeSetId($category->getDefaultAttributeSetId());

            if (isset($categoryPostData['category_products'])
                && is_string($categoryPostData['category_products'])
                && !$category->getProductsReadonly()
            ) {
                $products = json_decode($categoryPostData['category_products'], true);
                $category->setPostedProducts($products);
            }

            try {
                $this->_eventManager->dispatch(
                    'catalog_category_prepare_save',
                    ['category' => $category, 'request' => $this->getRequest()]
                );
                /**
                 * Check "Use Default Value" checkboxes values
                 */
                if (isset($categoryPostData['use_default']) && !empty($categoryPostData['use_default'])) {
                    foreach ($categoryPostData['use_default'] as $attributeCode => $attributeValue) {
                        if ($attributeValue) {
                            $category->setData($attributeCode, null);
                        }
                    }
                }

                /**
                 * Proceed with $_POST['use_config']
                 * set into category model for processing through validation
                 */
                $category->setData('use_post_data_config', $useConfig);

                $categoryResource = $category->getResource();
                if ($category->hasCustomDesignTo()) {
                    $categoryResource->getAttribute('custom_design_from')->setMaxValue($category->getCustomDesignTo());
                }
                $validate = $category->validate();
                if ($validate !== true) {
                    foreach ($validate as $code => $error) {
                        if ($error === true) {
                            $attribute = $categoryResource->getAttribute($code)->getFrontend()->getLabel();
                            throw new \Magento\Framework\Exception\LocalizedException(
                                __('The "%1" attribute is required. Enter and try again.', $attribute)
                            );
                        } else {
                            $this->messageManager->addErrorMessage(
                                __('Something went wrong while saving the category.')
                            );
                            $this->logger->critical('Something went wrong while saving the category.');
                            $this->_getSession()->setCategoryData($categoryPostData);
                        }
                    }
                }

                $category->unsetData('use_post_data_config');

                $transLandingPageId = $categoryPostData['trans_landing_page'];
                $allowSave = true;
                if ($transLandingPageId) {
                    //Case In Customer Store View
                    $stores = $this->storeManager->getStores();
                    //Case In Default Store View
                    $defaultStoreView = $this->storeManager->getStore(0);
                    if ($defaultStoreView) {
                        $stores[] = $defaultStoreView;
                    }
                    $hasLandingPage = false;
                    $categoryIdUsed = "";
                    foreach ($stores as $store) {
                        $categoryCollection = $this->categoryCollection->create()
                            ->setStore($store)->addAttributeToFilter('trans_landing_page', $transLandingPageId);
                        if (isset($categoryPostData['entity_id'])) {
                            $categoryCollection = $categoryCollection
                                ->addAttributeToFilter('entity_id', array('neq' => $categoryPostData['entity_id']));
                        }
                        if ($categoryCollection->getSize()) {
                            $categoryIdUsed = $categoryCollection->getFirstItem()->getData('entity_id');
                            $hasLandingPage = true;
                            break;
                        }
                    }
                    if ($hasLandingPage) {
                        $this->messageManager->addErrorMessage(__('Landing Page Used In Category Id: %1.', $categoryIdUsed));
                        $allowSave = false;
                    }
                }

                if ($allowSave) {
                    $category->save();
                    $this->_eventManager->dispatch(
                        'adminhtml_catalog_category_after_save',
                        ['category' => $category, 'request' => $this->getRequest()]
                    );

                    $this->messageManager->addSuccessMessage(__('You saved the category.'));
                }
                // phpcs:disable Magento2.Exceptions.ThrowCatch
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addExceptionMessage($e);
                $this->logger->critical($e);
                $this->_getSession()->setCategoryData($categoryPostData);
                // phpcs:disable Magento2.Exceptions.ThrowCatch
            } catch (\Throwable $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while saving the category.'));
                $this->logger->critical($e);
                $this->_getSession()->setCategoryData($categoryPostData);
            }
        }

        $hasError = (bool)$this->messageManager->getMessages()->getCountByType(
            \Magento\Framework\Message\MessageInterface::TYPE_ERROR
        );

        if ($this->getRequest()->getPost('return_session_messages_only')) {
            $category->load($category->getId());
            // to obtain truncated category name
            /** @var $block \Magento\Framework\View\Element\Messages */
            $block = $this->layoutFactory->create()->getMessagesBlock();
            $block->setMessages($this->messageManager->getMessages(true));

            /** @var \Magento\Framework\Controller\Result\Json $resultJson */
            $resultJson = $this->resultJsonFactory->create();
            return $resultJson->setData(
                [
                    'messages' => $block->getGroupedHtml(),
                    'error' => $hasError,
                    'category' => $category->toArray(),
                ]
            );
        }

        $redirectParams = $this->getRedirectParams($isNewCategory, $hasError, $category->getId(), $parentId, $storeId);

        return $resultRedirect->setPath(
            $redirectParams['path'],
            $redirectParams['params']
        );
    }
}
