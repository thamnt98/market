<?php

namespace SM\Brand\Api\Data;

interface BrandInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const BANNER         = 'banner';
    const CATEGORIES     = 'categories';
    const MOST_POPULAR   = 'most_popular';

    /**
     * @return \SM\Category\Api\Data\Catalog\CategoryInterface[]
     */
    public function getCategories();

    /**
     * @param $data
     * @return \SM\Category\Api\Data\Catalog\CategoryInterface[]
     */
    public function setCategories($data);

    /**
     * @return \SM\MobileApi\Api\Data\Product\ListInterface
     */
    public function getMostPopular();

    /**
     * @param $data
     * @return \SM\MobileApi\Api\Data\Product\ListInterface
     */
    public function setMostPopular($data);

    /**
     * @return \Amasty\ShopbyBase\Api\Data\OptionSettingInterface
     */
    public function getBanner();

    /**
     * @param $data
     * @return \Amasty\ShopbyBase\Api\Data\OptionSettingInterface
     */
    public function setBanner($data);
}
