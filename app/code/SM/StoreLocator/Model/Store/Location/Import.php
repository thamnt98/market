<?php

namespace SM\StoreLocator\Model\Store\Location;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\File\Csv as CsvFile;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Message\ManagerInterface;
use SM\StoreLocator\Model\Store\ResourceModel\Location as StoreLocationResource;

/**
 * Class Import
 * @package SM\StoreLocator\Model\Store\Location
 */
class Import
{
    /**
     * @var CsvFile
     */
    private $csv;

    /**
     * @var StoreLocationResource
     */
    private $storeLocationResource;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var JsonHelper
     */
    private $jsonHelper;

    /**
     * @param CsvFile $csv
     * @param StoreLocationResource $storeLocationResource
     * @param ManagerInterface $messageManager
     * @param JsonHelper $jsonHelper
     */
    public function __construct(
        CsvFile $csv,
        StoreLocationResource $storeLocationResource,
        ManagerInterface $messageManager,
        JsonHelper $jsonHelper
    ) {
        $this->csv = $csv;
        $this->storeLocationResource = $storeLocationResource;
        $this->messageManager = $messageManager;
        $this->jsonHelper = $jsonHelper;
    }

    /**
     * {@inheritdoc}
     * @throws LocalizedException
     * @throws Exception
     */
    public function import(string $filePath, $removeAll = false)
    {
        $csvData = $this->csv->getData($filePath);

        if ($removeAll) {
            $this->storeLocationResource->cleanTable();
        }

        $header = array_shift($csvData);

        foreach ($csvData as $row => $data) {
            $dataToImport = [
                'store_code' => $data[0],
                'name' => $data[1],
                'address_line_1' => $data[2],
                'address_line_2' => $data[3],
                'city' => $data[8],
                'district_id' => $data[9],
                'lat' => $data[12],
                'long' => $data[13],
                'opening_hours' => $this->getHours($data)
            ];
            $this->storeLocationResource->importRow($dataToImport);
        }
        return true;
    }

    public function getHours($data)
    {
        return $this->jsonHelper->jsonEncode([
           "Monday" => $this->parseTime($data[20]),
           "Tuesday" => $this->parseTime($data[21]),
           "Wednesday" => $this->parseTime($data[22]),
           "Thursday" => $this->parseTime($data[23]),
           "Friday" => $this->parseTime($data[24]),
           "Saturday" => $this->parseTime($data[25]),
           "Sunday" => $this->parseTime($data[19]),
        ]);
    }

    public function parseTime($data)
    {
        $time = explode('-', $data);
        if (is_array($time) && count($time) > 1) {
            return [
                'start' => $time[0],
                'end' => $time[1]
            ];
        }
    }
}
