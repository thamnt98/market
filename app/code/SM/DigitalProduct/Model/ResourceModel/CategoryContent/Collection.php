<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Model\ResourceModel\CategoryContent;

/**
 * Class Collection
 * @package SM\DigitalProduct\Model\ResourceModel\CategoryContent
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'category_store_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \SM\DigitalProduct\Model\CategoryContent::class,
            \SM\DigitalProduct\Model\ResourceModel\CategoryContent::class
        );
    }

    public function selectWithCategory()
    {
        $this->getSelect()
            ->joinLeft(
                ["category" => "sm_digitalproduct_category"],
                "category.category_id = main_table.category_id"
            );
        return $this;
    }
}

