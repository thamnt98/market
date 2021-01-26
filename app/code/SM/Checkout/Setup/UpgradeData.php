<?php
declare(strict_types=1);

namespace SM\Checkout\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    protected $moduleReader;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $fileDriver;

    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $fileCsv;

    /**
     * UpgradeData constructor.
     * @param \Magento\Framework\Module\Dir\Reader $moduleReader
     * @param \Magento\Framework\Filesystem\Driver\File $fileDriver
     * @param \Magento\Framework\File\Csv $fileCsv
     */
    public function __construct(
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        \Magento\Framework\File\Csv $fileCsv
    ) {
        $this->moduleReader = $moduleReader;
        $this->fileDriver = $fileDriver;
        $this->fileCsv = $fileCsv;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $table = $setup->getTable('omni_location_mapping');
            $updateCondition = [
                ['10019', '2294'],
                ['10011', '2314'],
                ['10051', '2306'],
                ['10020', '2318'],
                ['10057', '2324'],
                ['10015', '2297'],
                ['10115', '2833'],
                ['40015', '2829'],
                ['40014', '2822'],
                ['10109', '2782'],
                ['10099', '4144'],
                ['10012', '2317'],
                ['10027', '2325'],
                ['10021', '2299'],
                ['10072', '2298'],
                ['10036', '2303'],
                ['10080', '2306'],
                ['10059', '2321'],
                ['10025', '2294'],
                ['10016', '2324'],
                ['10087', '2291'],
                ['10039', '2822'],
                ['10028', '4139'],
                ['10066', '7059'],
                ['10054', '2295'],
                ['10075', '4121'],
                ['10018', '2315']
            ];
            $where = '';
            foreach ($updateCondition as $condition) {
                if ($where != '') {
                    $where .= ' OR ';
                }
                $where .= '(omni_code = ' . $condition[0] . ' AND district_id = ' . $condition[1] . ')';
            }
            $setup->getConnection()->update($table, ['support_shipping' => 1], $where);
        }
        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $table = $setup->getTable('omni_location_mapping');
            $data = [
                ['10019', '2294', '12310'],
                ['10011', '2314', '10510'],
                ['10051', '2306', '13440'],
                ['10020', '2318', '11610'],
                ['10057', '2324', '14440'],
                ['10015', '2297', '12770'],
                ['10115', '2833', '16454'],
                ['40015', '2829', '16430'],
                ['40014', '2822', '17113'],
                ['10109', '2782', '16166'],
                //['10099', '4144', '15220'],
                ['10012', '2317', '10130'],
                ['10027', '2325', '14420'],
                ['10021', '2299', '12950'],
                ['10072', '2298', '12870'],
                ['10036', '2303', '13560'],
                ['10080', '2306', '13450'],
                ['10059', '2321', '11470'],
                ['10025', '2294', '12210'],
                ['10016', '2324', '14450'],
                ['10087', '2291', '12560'],
                ['10039', '2822', '17113'],
                ['10028', '4139', '15322'],
                ['10066', '7059', '16915'],
                ['10054', '2295', '12610'],
                ['10075', '4121', '15117'],
                ['10018', '2315', '10640'],
                ['40017', '4143', '15220']
            ];
            $insertDataOmniLocationMapping = [];
            foreach ($data as $condition) {
                $select = $setup->getConnection()->select()->from(
                    [$table],
                    ['omni_code']
                )
                    ->where('omni_code = ?', $condition[0])
                    ->where('district_id = ?', $condition[1]);
                $checkIsset = $setup->getConnection()->fetchCol($select);
                if (empty($checkIsset)) {
                    $insertDataOmniLocationMapping[] = [$condition[0], $condition[1]];
                }
            }
            if (!empty($insertDataOmniLocationMapping)) {
                $setup->getConnection()->insertArray($table, ['omni_code', 'district_id'], $insertDataOmniLocationMapping);
            }
        }
        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            $tableName = $setup->getTable('omni_shipping_postcode');
            $setup->getConnection()->truncateTable($tableName);
            $directory = $this->moduleReader->getModuleDir('etc', 'SM_Checkout');
            $file = $directory . '/ecommerce-coverage-area.csv';
            try {
                if ($this->fileDriver->isExists($file)) {
                    $data = $this->fileCsv->getData($file);
                    unset($data[0]);
                    $columns = ['post_code', 'sub_district', 'district', 'jenis', 'city', 'regency'];
                    $setup->getConnection()->insertArray($tableName, $columns, $data);

                }
            } catch (\Exception $e) {
            }
        }

        if (version_compare($context->getVersion(), '1.0.6', '<')) {
            $tableName = $setup->getTable('omni_shipping_postcode');
            $setup->getConnection()->truncateTable($tableName);
            $directory = $this->moduleReader->getModuleDir('etc', 'SM_Checkout');
            $file = $directory . '/ecommerce-coverage-area-update.csv';
            try {
                if ($this->fileDriver->isExists($file)) {
                    $data = $this->fileCsv->getData($file);
                    unset($data[0]);
                    $columns = ['post_code', 'sub_district', 'district', 'jenis', 'city', 'regency'];
                    $setup->getConnection()->insertArray($tableName, $columns, $data);

                }
            } catch (\Exception $e) {
            }
        }
    }
}
