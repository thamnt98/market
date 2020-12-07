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
     * HomePage constructor.
     * @param HomeSliderFactory $homeSliderFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param ProductInterface $product
     * @param CollectionFactory $categoryCollection
     * @param Banner $banner
     * @param HomepageMessage $homepageMessage
     */
    public function __construct(
        HomeSliderFactory $homeSliderFactory,
        ScopeConfigInterface $scopeConfig,
        ProductInterface $product,
        CollectionFactory $categoryCollection,
        Banner $banner,
        HomepageMessage $homepageMessage
    ) {
        $this->homeSlider         = $homeSliderFactory;
        $this->scopeConfig        = $scopeConfig;
        $this->productInterface   = $product;
        $this->categoryCollection = $categoryCollection;
        $this->banner             = $banner;
        $this->greetingMessage    = $homepageMessage;
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

        return $this->productInterface->getList($category_id, 12, 1, false);
    }

    /**
     * @inheritDoc
     */
    public function getGreetingMessage($customerId)
    {
        return $this->greetingMessage->getMessage($customerId);
    }
}
