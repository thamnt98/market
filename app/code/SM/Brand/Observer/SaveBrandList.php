<?php

/**
 * @category SM
 * @package SM_Brand
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author      Chinhvd <chinhvd@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Brand\Observer;

use Amasty\ShopbyBrand\Model\ResourceModel\Slider\Grid\Collection;

class SaveBrandList implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $jsonManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \SM\Brand\Model\Category\BrandListFactory
     */
    protected $brandListFactory;

    /**
     * @var Collection
     */
    protected $sliderCollection;

    /**
     * @param Collection $sliderCollection
     * @param \SM\Brand\Model\Category\BrandListFactory $brandListFactory
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonManager
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        Collection $sliderCollection,
        \SM\Brand\Model\Category\BrandListFactory $brandListFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Serialize\Serializer\Json $jsonManager,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->jsonManager = $jsonManager;
        $this->logger = $logger;
        $this->brandListFactory = $brandListFactory;
        $this->sliderCollection = $sliderCollection;
    }

    /**
     * Stores category product positions in session
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = $observer->getEvent()->getCategory();
        /** @var \Magento\Framework\App\Request\Http $request */
        $request = $observer->getEvent()->getRequest();
        $data = $request->getParam(
            \SM\Brand\Block\Adminhtml\Category\BrandList::FIELD_NAME
        );
        $oldData = $request->getParam(
            \SM\Brand\Block\Adminhtml\Category\BrandList::OLD_DATA_FIELD_NAME
        );

        try {
            $data = $this->jsonManager->unserialize($data);
            $oldData = $this->jsonManager->unserialize($oldData);
            if (is_array($data)) {
                $this->removeOldData($category->getId(), $oldData, $data);
                foreach ($data as $code => $position) {
                    $model = $this->getModel($category->getId(), $code);
                    $model->setData('category_id', $category->getId())
                        ->setData('option_setting_id', $code)
                        ->setData('position', $position)
                        ->setData('title', $this->getTitle($code))
                        ->save();
                }
            }
        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage());
        }
    }

    /**
     * @param $categoryId
     * @param $optionId
     *
     * @return \SM\Brand\Model\Category\BrandList
     */
    protected function getModel($categoryId, $optionId)
    {
        /** @var \SM\Brand\Model\Category\BrandList $model */
        $model = $this->brandListFactory->create();
        $coll = $model->getCollection();
        $coll->getSelect()
            ->where('main_table.category_id = ?', $categoryId)
            ->where('main_table.option_setting_id  = ?', $optionId);

        if ($coll->count()) {
            return $coll->getFirstItem();
        }

        return $model;
    }

    /**
     * @param $categoryId
     * @param $oldData
     * @param $newData
     */
    protected function removeOldData($categoryId, $oldData, $newData)
    {
        $delete = [];
        foreach (array_keys($oldData) as $item) {
            if (!key_exists($item, $newData)) {
                $delete[] = $item;
            }
        }

        if (count($delete)) {
            $this->brandListFactory->create()->getResource()->removeCategoryItem($categoryId, $delete);
        }
    }

    /**
     * @param $optionId
     * @return array|mixed|null
     */
    protected function getTitle($optionId)
    {
        $slider = $this->sliderCollection->getItemById($optionId);
        return $slider->getData('title');
    }
}
