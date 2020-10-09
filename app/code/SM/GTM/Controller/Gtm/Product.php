<?php


namespace SM\GTM\Controller\Gtm;


use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use SM\GTM\Block\Product\View;
use Magento\Catalog\Model\ProductRepository;

class Product extends \Magento\Framework\App\Action\Action
{
    /**
     * @var View
     */
    private $productHelper;
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * Product constructor.
     * @param ProductRepository $productRepository
     * @param View $productHelper
     * @param Context $context
     */
    public function __construct(
        ProductRepository $productRepository,
        View $productHelper,
        Context $context
    ) {
        parent::__construct($context);
        $this->productHelper = $productHelper;
        $this->productRepository = $productRepository;
    }

    public function execute()
    {
        $productsInfo = $this->getRequest()->getPostValue();
        $data = [];
        if (!empty($productsInfo['productsInfo'])) {
            foreach ($productsInfo['productsInfo'] as $productInfo) {
                if (!empty($productInfo['productId'])) {
                    $product = $this->productRepository->getById($productInfo['productId']);
                    if (!empty($productInfo['productQty']) && !empty($productInfo['delivery_option'])) {
                        $data[] = $this->productHelper->getGtmData($product, $productInfo['productQty'], $productInfo['delivery_option']);
                    } else {
                        $data[] = $this->productHelper->getGtmData($product);
                    }
                } else if (!empty($productInfo['productSku'])) {
                    $product = $this->productRepository->get($productInfo['productSku']);
                    $data[] = $this->productHelper->getGtmData($product);
                }
            }
        }
        $resultJson = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
        $resultJson->setData($data);
        $resultJson->setHttpResponseCode(200);

        return $resultJson;
    }
}
