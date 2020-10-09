<?php

/**
 * @category SM
 * @package SM_Brand
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author      Chinhvd <chinhvd@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Brand\Model;

use Amasty\ShopbyBase\Api\Data\OptionSettingInterface;
use Amasty\ShopbyBase\Model\OptionSettingFactory;
use Amasty\ShopbyBase\Model\ResourceModel\OptionSetting\CollectionFactory as OptionSettingCollectionFactory;
use Amasty\ShopbyBrand\Helper\Data as DataHelper;
use Magento\Catalog\Model\Product\Attribute\Repository;
use Magento\Eav\Model\Entity\Attribute\Option;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\TestFramework\Inspection\Exception;
use SM\Brand\Api\BrandSliderRepositoryInterface;
use Magento\Framework\View\Element\Template;
use SM\Brand\Model\Category\BrandListFactory;

class BrandSliderRepository extends Template implements BrandSliderRepositoryInterface
{
    const PATH_BRAND_ATTRIBUTE_CODE = 'amshopby_brand/general/attribute_code';

    /**
     * @var  array|null
     */
    protected $items;

    /**
     * @var  Repository
     */
    protected $repository;

    /**
     * @var DataHelper
     */
    protected $helper;

    /**
     * @var OptionSettingCollectionFactory
     */
    private $optionSettingCollectionFactory;

    /**
     * @var OptionSettingFactory
     */
    private $optionSettingFactory;

    /**
     * @var array
     */
    private $settingByValue = [];

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
     * @param Context $context
     * @param Repository $repository
     * @param OptionSettingFactory $optionSettingFactory
     * @param OptionSettingCollectionFactory $optionSettingCollectionFactory
     * @param BrandListFactory $brandListFactory
     * @param DataHelper $helper
     * @param ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Repository $repository,
        OptionSettingFactory $optionSettingFactory,
        OptionSettingCollectionFactory $optionSettingCollectionFactory,
        BrandListFactory $brandListFactory,
        DataHelper $helper,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->brandListFactory = $brandListFactory;
        $this->repository = $repository;
        $this->helper = $helper;
        $this->optionSettingCollectionFactory = $optionSettingCollectionFactory;
        $this->optionSettingFactory = $optionSettingFactory;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }

    /**
     * @param int $categoryId
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBrandSlider($categoryId)
    {
        try {
            if ($this->items === null) {
                $this->items = [];
                $attributeCode = $this->helper->getBrandAttributeCode();
                if (!$attributeCode) {
                    return $this->items;
                }

                $options = $this->repository->get($attributeCode)->getOptions();
                array_shift($options);

                foreach ($options as $option) {
                    $setting = $this->getBrandOptionSettingByValue($option->getValue(), $categoryId);
                    $data = $this->getItemData($option, $setting, $categoryId);
                    if ($data && $data['campaign_id']) {
                        $this->items[] = $data;
                    }
                }

            }
            usort($this->items, [$this, '_sortByPosition']);
            return $this->items;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @param array $a
     * @param array $b
     *
     * @return int
     */
    protected function _sortByPosition($a, $b)
    {
        return $a['position'] - $b['position'];
    }

    /**
     * @param \Magento\Eav\Model\Entity\Attribute\Option $option
     * @param OptionSettingInterface $setting
     *
     * @param $categoryId
     * @return array
     */
    protected function getItemData(Option $option, OptionSettingInterface $setting, $categoryId)
    {
        $result = [];
        $brands = $this->getOption($categoryId, $setting->getId());
        foreach ($brands as $brand) {
            $result = [
                'campaign_id' => (int)$setting->getCampaignId() != 0 ? (int)$setting->getCampaignId() : null,
                'id'          => (int)$setting->getId(),
                'label'       => $setting->getLabel() ?: $option->getLabel(),
                'url'         => $this->helper->getBrandUrl($option),
                'img'         => $setting->getSliderImageUrl(),
                'position'    => (int)$brand->getData('position'),
                'alt'         => $setting->getSmallImageAlt() ? : $setting->getLabel()
            ];
        }



        return $result;
    }

    /**
     * @param int $value
     * @param int $categoryId
     * @return OptionSettingInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getBrandOptionSettingByValue($value, $categoryId)
    {
        if (empty($this->settingByValue)) {
            $stores = [0,  $this->_storeManager->getStore()->getId()];
            $collection = $this->optionSettingCollectionFactory->create()
                ->addFieldToFilter('store_id', $stores)
                ->addFieldToFilter('option_setting_id', $this->getOptionIds($categoryId))
                ->addOrder('store_id', 'ASC'); //current store values will rewrite defaults
            foreach ($collection as $item) {
                $this->settingByValue[$item->getValue()] = $item;
            }
        }

        return isset($this->settingByValue[$value])
            ? $this->settingByValue[$value] : $this->optionSettingFactory->create() ;
    }

    /**
     * @param int $categoryId
     * @return array
     */
    protected function getOptionIds($categoryId)
    {
        $data = array();
        foreach ($this->getBrandCollection($categoryId) as $item) {
            array_push($data, $item->getData('option_setting_id'));
        }
        return $data;
    }

    /**
     * @param int $categoryId
     * @param int $optionId
     * @return ResourceModel\Category\BrandList\Collection
     */
    protected function getOption($categoryId, $optionId)
    {
        return $this->getBrandCollection($categoryId)
            ->addFieldToFilter('option_setting_id', $optionId);
    }

    /**
     * @param int $categoryId
     * @return ResourceModel\Category\BrandList\Collection
     */
    protected function getBrandCollection($categoryId)
    {
        /** @var \SM\Brand\Model\ResourceModel\Category\BrandList\Collection $collection */
        $collection = $this->brandListFactory->create()->getCollection();
        $collection->addFieldToFilter('category_id', $categoryId);
        $collection->setOrder('position', 'ASC');
        $collection->setPageSize($this->getItemNumber());
        return  $collection;
    }

    /**
     * @return int
     */
    protected function getItemNumber()
    {
        return $this->scopeConfig->getValue(
            'amshopby_brand/slider/items_number_for_mobile',
            ScopeInterface::SCOPE_STORE
        );
    }
}
