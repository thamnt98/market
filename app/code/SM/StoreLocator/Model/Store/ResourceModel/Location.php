<?php

namespace SM\StoreLocator\Model\Store\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Location
 * @package SM\StoreLocator\Model\Store\ResourceModel
 */
class Location extends AbstractDb
{

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('transmart_store_location', 'place_id');
    }

    /**
     * @throws LocalizedException
     */
    public function cleanTable()
    {
        $connection = $this->getConnection();
        $connection->delete($this->getMainTable());
    }

    /**
     * @param array $rowData
     *
     * @throws LocalizedException
     */
    public function importRow(array $rowData)
    {
        $connection = $this->getConnection();
        $connection->insertOnDuplicate($this->getMainTable(), $rowData, array_keys($rowData));
    }

    /**
     * @param string $keyword
     * @return array
     * @throws LocalizedException
     * @codeCoverageIgnore
     */
    public function searchStoresByKeyWord(string $keyword): array
    {
        $keyword = '%' . $keyword . '%';
        $connection = $this->getConnection();

        $sql = $connection->select()
            ->from(
                ['main_table' => $this->getMainTable()],
                ['main_table.place_id']
            )
            ->where('main_table.name LIKE ?', $keyword)
            ->orWhere('main_table.address_line_1 LIKE ?', $keyword)
            ->orWhere('main_table.address_line_2 LIKE ?', $keyword);
        return $connection->fetchCol($sql);
    }
}
