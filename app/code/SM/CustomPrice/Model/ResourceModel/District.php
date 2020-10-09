<?php


namespace SM\CustomPrice\Model\ResourceModel;


use Magento\Customer\Model\Session;
use Magento\Framework\Model\ResourceModel\Db\Context;

class District extends \Trans\LocationCoverage\Model\ResourceModel\District
{
    const TABLE_NAME = 'omni_location_mapping';
    public function getOmniStoreCodeByDistrictId($district_id)
    {
        $connection = $this->getConnection();
        $tableName = $connection->getTableName(self::TABLE_NAME);
        if (!$connection->isTableExists($tableName)) {
            return null;
        }
        $query      = $connection->select()->from([
            'm' => $this->getTable(self::TABLE_NAME),
        ])
                                 ->where('district_id = ?', $district_id);
        $result     = $connection->fetchRow($query);
        return $result['omni_code'] ?? null;
    }
}
