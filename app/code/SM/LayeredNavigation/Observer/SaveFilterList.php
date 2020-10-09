<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_LayeredNavigation
 *
 * Date: April, 27 2020
 * Time: 6:11 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\LayeredNavigation\Observer;

class SaveFilterList implements \Magento\Framework\Event\ObserverInterface
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
     * @var \SM\LayeredNavigation\Model\Category\FilterListFactory
     */
    protected $filterListFactory;

    /**
     * @param \SM\LayeredNavigation\Model\Category\FilterListFactory $filterListFactory
     * @param \Magento\Catalog\Model\CategoryFactory                 $categoryFactory
     * @param \Magento\Framework\Serialize\Serializer\Json           $jsonManager
     * @param \Psr\Log\LoggerInterface                               $logger
     */
    public function __construct(
        \SM\LayeredNavigation\Model\Category\FilterListFactory $filterListFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Serialize\Serializer\Json $jsonManager,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->jsonManager = $jsonManager;
        $this->logger = $logger;
        $this->filterListFactory = $filterListFactory;
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
            \SM\LayeredNavigation\Block\Adminhtml\Category\FilterList::FIELD_NAME
        );
        $oldData = $request->getParam(
            \SM\LayeredNavigation\Block\Adminhtml\Category\FilterList::OLD_DATA_FIELD_NAME
        );

        try {
            $data = $this->jsonManager->unserialize($data);
            $oldData = $this->jsonManager->unserialize($oldData);
            if (is_array($data)) {
                $this->removeOldData($category->getId(), $oldData, $data);
                foreach ($data as $code => $position) {
                    $model = $this->getModel($category->getId(), $code);
                    $model->setData('category_id', $category->getId())
                        ->setData('attribute_code', $code)
                        ->setData('position', $position)
                        ->save();
                }
            }
        } catch (\Exception $e) {
            $this->logger->warning($e->getMessage());
        }
    }

    /**
     * @param $categoryId
     * @param $attributeCode
     *
     * @return \SM\LayeredNavigation\Model\Category\FilterList
     */
    protected function getModel($categoryId, $attributeCode)
    {
        /** @var \SM\LayeredNavigation\Model\Category\FilterList $model */
        $model = $this->filterListFactory->create();
        $coll = $model->getCollection();
        $coll->getSelect()
            ->where('main_table.category_id = ?', $categoryId)
            ->where('main_table.attribute_code  = ?', $attributeCode);

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
            $this->filterListFactory->create()->getResource()->removeCategoryItem($categoryId, $delete);
        }
    }
}
