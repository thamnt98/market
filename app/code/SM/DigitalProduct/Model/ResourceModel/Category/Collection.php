<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Model\ResourceModel\Category;

use SM\DigitalProduct\Api\Data\CategoryInterface;
use SM\DigitalProduct\Api\Data\CategoryContentInterface;

/**
 * Class Collection
 * @package SM\DigitalProduct\Model\ResourceModel\Category
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'category_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \SM\DigitalProduct\Model\Category::class,
            \SM\DigitalProduct\Model\ResourceModel\Category::class
        );
    }

    protected function _initSelect()
    {
        $this->addFilterToMap('category_id', 'main_table.category_id');
        parent::_initSelect();
        return $this;
    }

    public function loadContentByStoreId($storeId)
    {
        $this->getSelect()
            ->joinLeft(
                ["content" => "sm_digitalproduct_category_store"],
                "main_table.category_id = content.category_id AND content.store_id = " . $storeId,
                [
                    CategoryContentInterface::INFO,
                    CategoryContentInterface::INFORMATION,
                    CategoryContentInterface::TOOLTIP,
                ]
            );
        return $this;
    }
}
