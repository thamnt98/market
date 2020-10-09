<?php
/**
 * Class ElectricityBill
 * @package SM\DigitalProduct\Model\Api\Inquire
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */


namespace SM\DigitalProduct\Model\Api\Inquire;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use \SM\DigitalProduct\Api\Inquire\Data\ElectricityBillInterfaceFactory;
use \SM\DigitalProduct\Api\Inquire\Data\ElectricityBillInterface as ElectricityBillData;
use SM\DigitalProduct\Api\Inquire\Data\ElectricityTokenInterface as ElectricityTokenData;
use SM\DigitalProduct\Api\ReorderRepositoryInterface;
use Trans\DigitalProduct\Model\DigitalProductInquire;

class ElectricityBill extends AbstractElectricity implements \SM\DigitalProduct\Api\Inquire\ElectricityBillInterface
{
    /**
     * @var ElectricityBillInterfaceFactory
     */
    private $electricityBillFactory;

    /**
     * ElectricityBill constructor.
     * @param DigitalProductInquire $digitalProductInquire
     * @param ProductCollectionFactory $productCollectionFactory
     * @param ElectricityBillInterfaceFactory $electricityBillFactory
     */
    public function __construct(
        DigitalProductInquire $digitalProductInquire,
        ProductCollectionFactory $productCollectionFactory,
        ElectricityBillInterfaceFactory $electricityBillFactory
    ) {
        $this->electricityBillFactory = $electricityBillFactory;
        parent::__construct($digitalProductInquire, $productCollectionFactory);
    }

    /**
     * @inheritDoc
     */
    public function inquire($customerId, $customerNumber, $productId)
    {
        $result = $this->digitalProductInquire->electricityPostpaid($customerId, $customerNumber, $productId);
        return $this->prepareData($result, $productId, $customerNumber);
    }

    /**
     * @param $result
     * @param $productId
     * @param $customerNumber
     * @return mixed
     * @throws \Exception
     */
    private function prepareData($result, $productId, $customerNumber)
    {
        if ($result == false) {
            $data = $this->convertDataResponseFalse();
        } else {
            $data = json_decode($result, true);
            if ($data[ElectricityBillData::RESPONSE_CODE] == ReorderRepositoryInterface::SUCCESS) {
                $data[ElectricityBillData::BILL]
                    = $data[ElectricityBillData::INCENTIVE_AND_TAX_FEE]
                    = $data[ElectricityBillData::PENALTY]
                    = 0;

                foreach ($data['bills'] as $bill) {
                    $data[ElectricityBillData::BILL] += $bill['total_electricity_bill'];
                    $data[ElectricityBillData::INCENTIVE_AND_TAX_FEE] += $bill['value_added_tax'];
                    $data[ElectricityBillData::PENALTY] += $bill['penalty_fee'];
                }

                if (count(($data['bills'])) == 1) {
                    $data[ElectricityBillData::PERIOD] = $this->convertDate($data['bills'][0]['bill_period']);
                } else {
                    $data[ElectricityBillData::PERIOD] = $this->convertDate($data['bills'][0]['bill_period'])
                        . " - " .$this->convertDate(($data['bills'][count($data['bills']) - 1]['bill_period']));
                }

                $product = $this->getProductByVendor($productId);
                if ($product->getId()) {
                    $data[ElectricityBillData::ADMIN_FEE] =   $product->getFinalPrice();
                    $data[ElectricityBillData::PRICE] = $product->getFinalPrice() + $data[ElectricityBillData::BILL];
                }

                $data[ElectricityBillData::CUSTOMER_ID] = $customerNumber;
                $data[ElectricityBillData::POWER] = $data[ElectricityBillData::POWER] . " VA";
            } else {
                $data['message'] = $this->handleMessage($data[ElectricityBillData::RESPONSE_CODE]);
            }
        }

        if ($data[ElectricityTokenData::RESPONSE_CODE] != \SM\DigitalProduct\Api\ReorderRepositoryInterface::SUCCESS) {
            throw new \Magento\Framework\Webapi\Exception(
                $data[ElectricityTokenData::MESSAGE],
                0,
                \Magento\Framework\Webapi\Exception::HTTP_BAD_REQUEST
            );
        }

        return $this->electricityBillFactory->create()->setData($data);
    }


    /**
     * @param string $responseCode
     * @return \Magento\Framework\Phrase
     */
    private function handleMessage(string $responseCode)
    {
        switch ($responseCode) {
            case ReorderRepositoryInterface::ALREADY_PAID:
                return __("Your bill already paid or not available");
            case ReorderRepositoryInterface::PROVIDER_CUT_OFF:
                return __("This service is not available during cut off/maintenance time");
            case ReorderRepositoryInterface::TIMEOUT_RESPONSE_CODE:
                return __("Please check the internet connection and try again");
            default:
                return __("Make sure you enter the correct number");
        }
    }

    /**
     * @param $billPeriod
     * @return false|string
     */
    private function convertDate($billPeriod)
    {
        $billYear = substr($billPeriod, 0, 4);
        $billMount = substr($billPeriod, 4, 2);
        return date("F Y", strtotime("{$billYear}-{$billMount}"));
    }
}
