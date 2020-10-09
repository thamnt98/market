<?php

namespace SM\Catalog\Controller\Product;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Result\PageFactory;
use Magento\Catalog\Controller\Product as ProductAction;

class View extends \Magento\Catalog\Controller\Product\View
{
    protected $logger;
    protected $jsonHelper;
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface
     */
    private $sourceItemsBySku;

    public function __construct(
        Context $context,
        \Magento\Catalog\Helper\Product\View $viewHelper,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        PageFactory $resultPageFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface $sourceItemsBySku,
        \Psr\Log\LoggerInterface $logger = null,
        \Magento\Framework\Json\Helper\Data $jsonHelper = null
    ) {
        $this->logger = $logger ?: ObjectManager::getInstance()
            ->get(\Psr\Log\LoggerInterface::class);
        $this->jsonHelper = $jsonHelper ?: ObjectManager::getInstance()
            ->get(\Magento\Framework\Json\Helper\Data::class);
        $this->productRepository = $productRepository;
        $this->sourceItemsBySku = $sourceItemsBySku;
        parent::__construct($context, $viewHelper, $resultForwardFactory, $resultPageFactory, $logger, $jsonHelper);
    }

    public function execute()
    {
        // Get initial data from request
        $categoryId = (int)$this->getRequest()->getParam('category', false);
        $productId = (int)$this->getRequest()->getParam('id');
        $specifyOptions = $this->getRequest()->getParam('options');

        if ($this->getRequest()->isPost() && $this->getRequest()->getParam(self::PARAM_NAME_URL_ENCODED)) {
            $product = $this->_initProduct();

            if (!$product) {
                return $this->noProductRedirect();
            }

            if ($specifyOptions) {
                $notice = $product->getTypeInstance()->getSpecifyOptionMessage();
                $this->messageManager->addNoticeMessage($notice);
            }

            if ($this->getRequest()->isAjax()) {
                $this->getResponse()->representJson(
                    $this->jsonHelper->jsonEncode(
                        [
                            'backUrl' => $this->_redirect->getRedirectUrl()
                        ]
                    )
                );
                return;
            }
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setRefererOrBaseUrl();
            return $resultRedirect;
        }

        // Prepare helper and params
        $params = new \Magento\Framework\DataObject();
        $params->setCategoryId($categoryId);
        $params->setSpecifyOptions($specifyOptions);

        // Render page
        try {
            if ($this->checkShowProduct($productId)) {
                $page = $this->resultPageFactory->create();
                $this->viewHelper->prepareAndRender($page, $productId, $this, $params);
                return $page;
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return $this->noProductRedirect();
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->forward('noroute');
            return $resultForward;
        }
    }

    public function checkShowProduct($id)
    {
        $product = $this->productRepository->getById($id);
        if ($product->getTypeId() == 'bundle') {
            $options = $product->getTypeInstance(true)->getSelectionsCollection(
                $product->getTypeInstance(true)->getOptionsIds($product),
                $product
            )->getItems();
            $stockArr = [];
            $count = 0;
            foreach ($options as $option) {
                if ($option->getTypeId() == 'configurable') {
                    $productOp = $this->productRepository->get($option->getSku());
                    $listChildProds = $productOp->getExtensionAttributes()->getConfigurableProductLinks();
                    $childSource = [];
                    $j = 0;
                    foreach ($listChildProds as $child) {
                        $emptyStock = false;
                        $product = $this->productRepository->getById($child);
                        $sources = $this->sourceItemsBySku->execute($product->getSku());

                        foreach ($sources as $source) {
                            if ($source->getStatus() > 0 && $source->getQuantity() > 0) {
                                $childSource[$j][] = $source->getSourceCode();
                            } else {
                                $emptyStock = true;
                            }
                        }
                        if ($emptyStock && !isset($childSource[$j])) {
                            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Empty Source'));
                        }
                        $j++;
                    }
                    $stocksChild = [];
                    for ($i = 0; $i < sizeof($childSource) - 1; $i++) {
                        if ($i == 0) {
                            $stocksChild = array_intersect($childSource[$i], $childSource[$i + 1]);
                        } else {
                            $stocksChild = array_intersect($childSource[$i], $childSource[$i + 1], $stocksChild);
                        }
                    }
                    if (empty($stocksChild)) {
                        throw new \Magento\Framework\Exception\NoSuchEntityException(__('Empty Common Sources'));
                    } else {
                        $stockArr[$count] = $stocksChild;
                    }

                } else {
                    $sources = $this->sourceItemsBySku->execute($option->getSku());
                    $emptyStock = false;
                    foreach ($sources as $source) {
                        if ($source->getStatus() > 0 && $source->getQuantity() > 0) {
                            $stockArr[$count][] = $source->getSourceCode();
                        } else {
                            $emptyStock = true;
                        }
                    }
                    if ($emptyStock && empty($stockArr[$count])) {
                        throw new \Magento\Framework\Exception\NoSuchEntityException(__('Empty Source'));
                    }
                }

                $count++;
            }
            $stocks = [];
            if (empty($stockArr)) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(__('Empty Source'));
            } else {
                for ($i = 0; $i < sizeof($stockArr) - 1; $i++) {
                    if ($i == 0) {
                        $stocks = array_intersect($stockArr[$i], $stockArr[$i + 1]);
                    } else {
                        $stocks = array_intersect($stockArr[$i], $stockArr[$i + 1], $stocks);
                    }
                }
            }
            if (empty($stocks)) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(__('Empty Common Sources'));
            }
        }
        return true;
    }
}
