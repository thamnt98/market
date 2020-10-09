<?php
namespace SM\Catalog\Controller\Product\Compare;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Customadd extends \Magento\Framework\App\Action\Action{

    /**
     * @var \Magento\Catalog\Helper\Product\Compare
     */
    protected $helper;
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_jsonHelper;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    const LIMIT_TO_COMPARE_PRODUCTS = 3;

    /**
     * @var Validator
     */
    protected $_formKeyValidator;
    /**
     * @var \Magento\Catalog\Model\Product\Compare\ListCompare
     */
    protected $_catalogProductCompareList;
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Customer\Model\Visitor
     */
    protected $_customerVisitor;

    public function __construct(Context $context,
                                Validator $formKeyValidator,
                                \Magento\Framework\Json\Helper\Data $jsonHelper,
                                \Magento\Catalog\Helper\Product\Compare $helper,
                                \Magento\Catalog\Model\Product\Compare\ListCompare $catalogProductCompareList,
                                ProductRepositoryInterface $productRepository,
                                \Magento\Customer\Model\Session $customerSession,
                                \Magento\Store\Model\StoreManagerInterface $storeManager,
                                \Magento\Customer\Model\Visitor $customerVisitor,
                                \Magento\Catalog\Model\ProductFactory $productFactory)
    {
        $this->helper = $helper;
        $this->_jsonHelper = $jsonHelper;
        $this->productFactory = $productFactory;
        $this->_formKeyValidator = $formKeyValidator;
        $this->_catalogProductCompareList = $catalogProductCompareList;
        $this->productRepository = $productRepository;
        $this->_customerSession = $customerSession;
        $this->_storeManager = $storeManager;
        $this->_customerVisitor = $customerVisitor;
        parent::__construct($context);
    }

    /**
     * Add item to compare list.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $addProductId =  $this->getRequest()->getParam("product");
        $allowRemove = $this->getRequest()->getParam("isremove");
        $count = $this->helper->getItemCount();
        $comepareList = $this->helper->getItemCollection();

        foreach ($comepareList as $product){
            if($product->getId() == $addProductId){
                $message = "This product already exist in compare list";
                $this->messageManager->addErrorMessage($message);

                /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setRefererOrBaseUrl();
            }
        }

        if($allowRemove == true){
            $tmp = $comepareList;
            foreach ($tmp as $product){
                $this->_catalogProductCompareList->removeProduct($product);
            }
            $count = 0;
        }

        if($count >= self::LIMIT_TO_COMPARE_PRODUCTS) {
            $message = "Your comparison list is already full (3 products)";
            $this->messageManager->addErrorMessage($message);

            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setRefererOrBaseUrl();
        }else {
            $productId = (int)$this->getRequest()->getParam('product');
            if ($productId && ($this->_customerVisitor->getId() || $this->_customerSession->isLoggedIn())) {
                $storeId = $this->_storeManager->getStore()->getId();
                try {
                    /** @var \Magento\Catalog\Model\Product $product */
                    $product = $this->productRepository->getById($productId, false, $storeId);
                } catch (NoSuchEntityException $e) {
                    $product = null;
                }

                if ($product) {
                    $this->_catalogProductCompareList->addProduct($product);
                    $productName = $this->_objectManager->get(
                        \Magento\Framework\Escaper::class
                    )->escapeHtml($product->getName());
                    $this->messageManager->addComplexSuccessMessage(
                        'addCompareSuccessMessage',
                        [
                            'product_name' => $productName,
                            'compare_list_url' => $this->_url->getUrl('catalog/product_compare'),
                        ]
                    );

                    $this->_eventManager->dispatch('catalog_product_compare_add_product', ['product' => $product]);
                }

                $this->_objectManager->get(\Magento\Catalog\Helper\Product\Compare::class)->calculate();
            }

            return $resultRedirect->setRefererOrBaseUrl();
        }
    }
}