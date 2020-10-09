<?php

namespace SM\MobileApi\Model;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use SM\HeroBanner\Model\Banner;
use SM\MobileApi\Api\HomeInterface;
use SM\MobileApi\Api\ProductInterface;
use SM\MobileApi\Model\Data\Slider\HomeSliderFactory;

/**
 * Class HomePage
 * @package SM\MobileApi\Model
 */
class HomePage implements HomeInterface
{
    const XML_PATH_MOST_POPULAR_HOME_PAGE   = 'sm_mobile/mobile_homepage/most_popular';
    const XML_PATH_SURPRISE_DEAL_HOME_PAGE  = 'sm_mobile/mobile_homepage/surprise_deal';

    protected $homeSlider;

    protected $scopeConfig;

    protected $productInterface;

    protected $categoryCollection;
    /**
     * @var Banner
     */
    protected $banner;

    public function __construct(
        HomeSliderFactory $homeSliderFactory,
        ScopeConfigInterface $scopeConfig,
        ProductInterface $product,
        CollectionFactory $categoryCollection,
        Banner $banner
    ) {
        $this->homeSlider           = $homeSliderFactory;
        $this->scopeConfig          = $scopeConfig;
        $this->productInterface     = $product;
        $this->categoryCollection   = $categoryCollection;
        $this->banner = $banner;
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
        $category_id = $this->scopeConfig->getValue(self::XML_PATH_MOST_POPULAR_HOME_PAGE);
        $categoryCollection = $this->categoryCollection->create()
                                ->addAttributeToFilter('entity_id', ['eq' => $category_id]);

        if (!$category_id && $categoryCollection->getSize() == 0) {
            return [];
        }

        return $this->productInterface->getList($category_id);
    }

    /**
     * @return array|\SM\MobileApi\Api\Data\Product\ListInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSurpriseDeal()
    {
        $category_id = $this->scopeConfig->getValue(self::XML_PATH_SURPRISE_DEAL_HOME_PAGE);
        $categoryCollection = $this->categoryCollection->create()
            ->addAttributeToFilter('entity_id', ['eq' => $category_id]);

        if (!$category_id && $categoryCollection->getSize() == 0) {
            return [];
        }

        return $this->productInterface->getList($category_id);
    }

    public function getDummyData()
    {
        return [
            [
                'imageUrl' => 'https://s3.envato.com/files/259651943/______%20__________-1.jpg',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit'
            ],
            [
                'imageUrl' => 'https://media.istockphoto.com/vectors/fresh-fruit-banner-food-icon-set-cartoon-vector-illustration-vector-id954842200',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit'
            ],
            [
                'imageUrl' => 'https://us.123rf.com/450wm/incomible/incomible1703/incomible170300174/75163577-banner-with-exotic-tropical-fruits-illustration-of-asian-plants.jpg?ver=6',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit'
            ],
            [
                'imageUrl' => 'https://us.123rf.com/450wm/alphaspirit/alphaspirit1612/alphaspirit161200152/69536208-colourful-banner-of-fruits-and-salad-on-white-background-healthy-food-concept.jpg?ver=6',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit'
            ],
        ];
    }
}
