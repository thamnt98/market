<?php
namespace SM\MobileApi\Model\Review;

/**
 * Class Submit
 * @package SM\MobileApi\Model\Review
 */
class Submit
{
    protected $eventManager;
    protected $request;
    protected $productRepository;
    protected $coreRegistry;
    protected $reviewFactory;
    protected $ratingFactory;
    protected $customerSession;
    protected $storeManager;

    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Webapi\Rest\Request $request,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Magento\Review\Model\RatingFactory $ratingFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->eventManager = $eventManager;
        $this->request = $request;
        $this->productRepository = $productRepository;
        $this->coreRegistry = $coreRegistry;
        $this->reviewFactory = $reviewFactory;
        $this->ratingFactory = $ratingFactory;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
    }

    /**
     * Submit new review action
     *
     * @return string
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function submit()
    {
        $data = $this->request->getParams();
        $rating = $this->request->getParam('ratings', []);

        if (($product = $this->_initProduct()) && !empty($data)) {
            $review = $this->reviewFactory->create()->setData($data);
            $review->unsetData('review_id');

            $validate = $review->validate();
            if ($validate === true) {
                try {
                    $review->setEntityId($review->getEntityIdByCode(\Magento\Review\Model\Review::ENTITY_PRODUCT_CODE))
                        ->setEntityPkValue($product->getId())
                        ->setStatusId(\Magento\Review\Model\Review::STATUS_PENDING)
                        ->setCustomerId($this->customerSession->getCustomerId())
                        ->setStoreId($this->storeManager->getStore()->getId())
                        ->setStores([$this->storeManager->getStore()->getId()])
                        ->save();

                    foreach ($rating as $ratingId => $optionId) {
                        $this->ratingFactory->create()
                            ->setRatingId($ratingId)
                            ->setReviewId($review->getId())
                            ->setCustomerId($this->customerSession->getCustomerId())
                            ->addOptionVote($optionId, $product->getId());
                    }

                    $review->aggregate();

                    return __('You submitted your review for moderation.');
                } catch (\Exception $e) {
                    throw new \Magento\Framework\Webapi\Exception(
                        __('We can\'t post your review right now.'),
                        0,
                        \Magento\Framework\Webapi\Exception::HTTP_INTERNAL_ERROR
                    );
                }
            } else {
                if (is_array($validate)) {
                    throw new \Magento\Framework\Webapi\Exception(
                        join("\n", $validate),
                        0,
                        \Magento\Framework\Webapi\Exception::HTTP_INTERNAL_ERROR
                    );
                } else {
                    throw new \Magento\Framework\Webapi\Exception(
                        __('We can\'t post your review right now.'),
                        0,
                        \Magento\Framework\Webapi\Exception::HTTP_INTERNAL_ERROR
                    );
                }
            }
        }
    }

    /**
     * Initialize and check product
     *
     * @return \Magento\Catalog\Model\Product|bool
     */
    protected function _initProduct()
    {
        $this->eventManager->dispatch('review_controller_product_init_before', ['controller_action' => $this]);
        $productId = (int)$this->request->getParam('product_id');

        $product = $this->_loadProduct($productId);
        if (!$product) {
            return false;
        }

        try {
            $this->eventManager->dispatch('review_controller_product_init', ['product' => $product]);
            $this->eventManager->dispatch(
                'review_controller_product_init_after',
                ['product' => $product, 'controller_action' => $this]
            );
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return false;
        }

        return $product;
    }

    /**
     * Load product model with data by passed id.
     * Return false if product was not loaded or has incorrect status.
     *
     * @param int $productId
     * @return bool|\Magento\Catalog\Model\Product
     */
    protected function _loadProduct($productId)
    {
        if (!$productId) {
            return false;
        }

        try {
            /* @var $product \Magento\Catalog\Model\Product */
            $product = $this->productRepository->getById($productId);
            if (!$product->isVisibleInCatalog() || !$product->isVisibleInSiteVisibility()) {
                throw new \Magento\Framework\Exception\NoSuchEntityException();
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $noEntityException) {
            return false;
        }

        $this->coreRegistry->register('current_product', $product);
        $this->coreRegistry->register('product', $product);

        return $product;
    }
}
