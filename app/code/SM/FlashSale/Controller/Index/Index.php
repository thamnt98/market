<?php
namespace SM\FlashSale\Controller\Index;
use Magento\Framework\Controller\ResultFactory;
use \Magento\CatalogEvent\Model\Event as SaleEvent;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;

    protected $_historyFactory;

    protected $_jsonHelper;

    protected $categoryEventList;

    protected $productCollectionFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \SM\FlashSale\Model\HistoryFactory $historyFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\CatalogEvent\Model\Category\EventList $categoryEventList,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
    )
    {
        $this->_pageFactory = $pageFactory;
        $this->_historyFactory = $historyFactory;
        $this->_jsonHelper = $jsonHelper;
        $this->categoryEventList = $categoryEventList;
        $this->productCollectionFactory = $productCollectionFactory ;
        return parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $response = $this->resultFactory->create(ResultFactory::TYPE_RAW);

        $event = $this->categoryEventList->getEventCollection()
            ->addFieldToFilter('status',SaleEvent::STATUS_OPEN)->addVisibilityFilter()->getFirstItem();
        $dataProduct = [];

        if($event->getData("category_id") && $event->getData() != null){

            $productCollection = $this->productCollectionFactory->create();
            $productCollection->addAttributeToSelect('*')
                ->addCategoriesFilter(["in" => $event->getData("category_id")])
                ->addFieldToFilter('is_flashsale',1);

            if($productCollection->getData()) {
                $history = $this->_historyFactory->create();
                foreach ($productCollection as $product) {

                    $collection = $history->getCollection()
                        ->addFieldToFilter('event_id', $event->getData("event_id"))
                        ->addFieldToFilter('item_id', $product->getId());

                    $itemTotalBuy = 0;
                    foreach ($collection as $historyItem) {
                        $itemTotalBuy += $historyItem->getData('item_qty_purchase');
                    }
                    $availableQty = $product->getData("flashsale_qty") - $itemTotalBuy;
                    if($availableQty <= 0) $availableQty = 0;
                    $dataProduct[] = ["productId" => $product->getId(), "saleQty" => $availableQty];
                }
            }
        }

        $response->setContents(
            $this->_jsonHelper->jsonEncode(
                [
                    'available_qty' => $dataProduct,
                ]
            )
        );

        return $response;
    }
}