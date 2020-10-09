<?php

namespace SM\Brand\Plugin\Block\Widget;

use Amasty\ShopbyBrand\Block\Widget\BrandListAbstract;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use SM\Brand\Api\BrandSliderRepositoryInterface;
use Magento\Framework\Registry;
use SM\Brand\Model\Category\BrandListFactory;

class BrandListAbstractPlugin
{
    /**
     * @var BrandSliderRepositoryInterface
     */
    protected $brandSliderRepository;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var BrandListFactory
     */
    protected $brandListFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     *
     * @param BrandListFactory $brandListFactory
     * @param BrandSliderRepositoryInterface $brandSliderRepository
     * @param ScopeConfigInterface $scopeConfig
     * @param Registry $registry
     */
    public function __construct(
        BrandListFactory $brandListFactory,
        BrandSliderRepositoryInterface $brandSliderRepository,
        ScopeConfigInterface $scopeConfig,
        Registry $registry
    ) {
        $this->scopeConfig           = $scopeConfig;
        $this->brandListFactory      = $brandListFactory;
        $this->brandSliderRepository = $brandSliderRepository;
        $this->registry              = $registry;
    }

    /**
     *
     * @param BrandListAbstract $subject
     * @param $result
     * @return \Amasty\ShopbyBase\Api\Data\OptionSettingInterface
     */
    public function afterGetItems(BrandListAbstract $subject, $result)
    {
        try {
            if ($category = $this->registry->registry('current_category')) {
                $categoryId = $category->getId();
                /** @var \SM\Brand\Model\ResourceModel\Category\BrandList\Collection $collection */
                $collection = $this->brandListFactory->create()->getCollection();
                $categoryIds = [];
                if ($collection) {
                    foreach ($collection as $item) {
                        $categoryIds[] = $item->getData('category_id');
                    }
                    if ($categoryId && in_array($categoryId, $categoryIds)) {
                        $brandList = $this->brandSliderRepository->getBrandSlider($category->getId());
                        return array_slice($brandList, 0, $this->getItemNumber());
                    }
                }
            }
        } catch (\Exception $e) {
        }

        return $result;
    }

    /**
     * @return int
     */
    protected function getItemNumber()
    {
        return $this->scopeConfig->getValue(
            'amshopby_brand/slider/items_number',
            ScopeInterface::SCOPE_STORE
        );
    }
}
