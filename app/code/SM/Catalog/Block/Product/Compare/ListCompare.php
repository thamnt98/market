<?php
declare(strict_types=1);

namespace SM\Catalog\Block\Product\Compare;
use SM\Catalog\Block\Product\ProductList\Item\AddTo\Iteminfo;

class ListCompare extends \Magento\Catalog\Block\Product\Compare\ListCompare{

    protected $categoryRepository;
    protected $compare;
    protected $postHelper;
    protected $resourceConnection;
    protected $itemInfo;
    protected $_reviewFactory;

    public function __construct(\Magento\Catalog\Block\Product\Context $context,
                                \Magento\Framework\Url\EncoderInterface $urlEncoder,
                                \Magento\Catalog\Model\ResourceModel\Product\Compare\Item\CollectionFactory $itemCollectionFactory,
                                \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
                                \Magento\Customer\Model\Visitor $customerVisitor,
                                \Magento\Framework\App\Http\Context $httpContext,
                                \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
                                \Magento\Catalog\Model\CategoryRepository $categoryRepository,
                                \Magento\Catalog\Helper\Product\Compare $compare,
                                \Magento\Framework\Data\Helper\PostHelper $postHelper,
                                Iteminfo $itemInfo,
                                \Magento\Review\Model\ReviewFactory $reviewFactory,
                                \Magento\Framework\App\ResourceConnection $resourceConnection,
                                array $data = [])
    {
        $this->categoryRepository = $categoryRepository;
        $this->compare = $compare;
        $this->postHelper = $postHelper;
        $this->resourceConnection = $resourceConnection;
        $this->itemInfo = $itemInfo;
        $this->_reviewFactory = $reviewFactory;

        parent::__construct($context, $urlEncoder, $itemCollectionFactory, $catalogProductVisibility, $customerVisitor, $httpContext, $currentCustomer, $data);
    }

    /**
     * Get newest item in compare list and get category
     * @return bool|\Magento\Catalog\Api\Data\CategoryInterface|mixed
     */
    public function getCurrentCategory(){
        $productLists = $this->_items;
        $catArray = [];
        $compareArray = [];
        $count = $this->_items->count();
        if($count < 3) {
            foreach ($productLists as $product) {
                $connection = $this->resourceConnection->getConnection();
                $sql = "SELECT category_id FROM catalog_category_product WHERE product_id = " . $product->getId();
                $currentCategoriesList = $connection->fetchAssoc($sql);
                if (empty($compareArray)) {
                    $compareArray = $currentCategoriesList;
                } else {
                    $catArray = array_intersect_key($compareArray, $currentCategoriesList);
                }
            }

            if($count >= 2) {
                $catArray = array_keys($catArray);
                if (empty($catArray)) {
                    return false;
                }
                if (($key = array_search(2, $catArray)) !== false) {
                    unset($catArray[$key]);
                }
                if (empty($compareArray)) {
                    return false;
                }
                $catArray = array_values($catArray);
                $category = $this->categoryRepository->get($catArray[0], $this->_storeManager->getStore()->getId());
            }else {
                $compareArray = array_keys($compareArray);
                if (empty($compareArray)) {
                    return false;
                }
                if (($key = array_search(2, $compareArray)) !== false) {
                    unset($compareArray[$key]);
                }
                if (empty($compareArray)) {
                    return false;
                }
                $compareArray = array_values($compareArray);
                $category = $this->categoryRepository->get($compareArray[0], $this->_storeManager->getStore()->getId());
            }

            return $category;
        }else return false;
    }

    public function getPostDataRemove($product){
        $data = [
            \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED => '',
            'product' => $product->getId()
        ];
        return $this->postHelper->getPostData($this->compare->getRemoveUrl(),$data);
    }


    public function getDiscountPercent($product)
    {
        if ($product != null) {
            return $this->itemInfo->getDiscountPercent($product);
        }
        return null;
    }


    public function hasAttributeValueForProducts($attribute)
    {
        foreach ($this->getItems() as $item) {
            if ($item->hasData($attribute->getAttributeCode())) {
                if($item->getData($attribute->getAttributeCode()) == null || $item->getData($attribute->getAttributeCode()) == "") return false;
                return true;
            }
        }
        return false;
    }

    public function getReview($product){
        $this->_reviewFactory->create()->getEntitySummary($product, $this->_storeManager->getStore()->getId());
        $ratingSummary = $product->getRatingSummary()->getRatingSummary();

        if($ratingSummary)
        return $ratingSummary;
        else return 0;
    }

    public function checkReview(){
        $countReview = 0;
        foreach ($this->getItems() as $item) {
            if($this->getReview($item) > 0){
                $countReview++;
            }
        }
        if($countReview > 0) $displayRating = true;
        else $displayRating = false;
        return $displayRating;
    }
}