<?php
namespace SM\Catalog\Controller\Product\Compare;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Check extends \Magento\Framework\App\Action\Action{

    /**
     * @var \Magento\Catalog\Helper\Product\Compare
     */
    protected $_helper;
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_jsonHelper;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * Check constructor.
     * @param Context $context
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Catalog\Helper\Product\Compare $helper
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     */
    public function __construct(Context $context,
                                \Magento\Framework\Json\Helper\Data $jsonHelper,
                                \Magento\Catalog\Helper\Product\Compare $helper,
                                \Magento\Framework\App\ResourceConnection $resourceConnection,
                                \Magento\Catalog\Model\ProductFactory $productFactory)
    {
        $this->_helper = $helper;
        $this->_jsonHelper = $jsonHelper;
        $this->productFactory = $productFactory;
        $this->resourceConnection = $resourceConnection;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $productId = $this->getRequest()->getParam('productId');

        $connection = $this->resourceConnection->getConnection();
        $sql = "SELECT category_id FROM catalog_category_product WHERE product_id = $productId";

        $currentCategoriesList = $connection->fetchAssoc($sql);

        $response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $productLists = $this->_helper->getItemCollection();
        $removeIds = array();

        foreach ($productLists as $compareProduct){
            if($compareProduct->getId() == $productId ) continue;
            $pId = $compareProduct->getId();
            $sql2 = "SELECT category_id FROM catalog_category_product WHERE product_id = $pId";
            $categoriesList = $connection->fetchAssoc($sql2);
            $compareArray = array_intersect_key($categoriesList, $currentCategoriesList);
            if(empty($compareArray)) $removeIds[] = $compareProduct->getId();
        }

        if(!empty($removeIds)) $clearList = true;
        else $clearList = false;
        $response->setContents(
            $this->_jsonHelper->jsonEncode(
                [
                    'clearList' => $clearList,
                ]
            )
        );

        return $response;
    }
}