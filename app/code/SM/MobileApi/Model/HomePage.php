<?php

namespace SM\MobileApi\Model;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use SM\HeroBanner\Model\Banner;
use SM\MobileApi\Api\HomeInterface;
use SM\MobileApi\Api\ProductInterface;
use SM\MobileApi\Model\Data\Slider\HomeSliderFactory;
use SM\MobileApi\Model\HomepageMessage;

/**
 * Class HomePage
 * @package SM\MobileApi\Model
 */
class HomePage implements HomeInterface
{
    const XML_PATH_MOST_POPULAR_HOME_PAGE  = 'sm_mobile/mobile_homepage/most_popular';

    /**
     * @var HomeSliderFactory
     */
    protected $homeSlider;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var ProductInterface
     */
    protected $productInterface;

    /**
     * @var CollectionFactory
     */
    protected $categoryCollection;

    /**
     * @var Banner
     */
    protected $banner;

    /**
     * @var HomepageMessage
     */
    protected $greetingMessage;

    /**
     * @var \Magento\Authorization\Model\UserContextInterface
     */
    protected $tokenUserContext;

    /**
     * HomePage constructor.
     * @param HomeSliderFactory $homeSliderFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param ProductInterface $product
     * @param CollectionFactory $categoryCollection
     * @param Banner $banner
     * @param \SM\MobileApi\Model\HomepageMessage $homepageMessage
     * @param \Magento\Authorization\Model\UserContextInterface $tokenUserContext
     */
    public function __construct(
        HomeSliderFactory $homeSliderFactory,
        ScopeConfigInterface $scopeConfig,
        ProductInterface $product,
        CollectionFactory $categoryCollection,
        Banner $banner,
        HomepageMessage $homepageMessage,
        \Magento\Authorization\Model\UserContextInterface $tokenUserContext
    ) {
        $this->homeSlider         = $homeSliderFactory;
        $this->scopeConfig        = $scopeConfig;
        $this->productInterface   = $product;
        $this->categoryCollection = $categoryCollection;
        $this->banner             = $banner;
        $this->greetingMessage    = $homepageMessage;
        $this->tokenUserContext  = $tokenUserContext;
    }

    /**
     * @return array|\SM\MobileApi\Api\Data\Slider\HomeSliderInterface[]
     */
    public function getHomeSlider()
    {
        return $this->banner->getBannersByCategoryId('home-page');
    }

    /**
     * @return array|\SM\MobileApi\Api\Data\Product\ListInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMostPopular()
    {
        $category_id        = $this->scopeConfig->getValue(self::XML_PATH_MOST_POPULAR_HOME_PAGE);
        $categoryCollection = $this->categoryCollection->create()
            ->addAttributeToFilter('entity_id', ['eq' => $category_id]);

        if (!$category_id && $categoryCollection->getSize() == 0) {
            return [];
        }
        $customerId = $this->tokenUserContext->getUserId();
        return $this->productInterface->getList($category_id, 12, 1, false, $customerId);
    }

    /**
     * @inheritDoc
     */
    public function getGreetingMessage()
    {
        return $this->greetingMessage->getMessage();
    }
}
