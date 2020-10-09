<?php
/**
 * Class ElectricityToken
 * @package SM\DigitalProduct\Model\Api\Inquire
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\DigitalProduct\Model\Api\Inquire;

use Magento\Setup\Exception;
use SM\DigitalProduct\Api\Inquire\Data\ElectricityBillInterface as ElectricityBillData;
use SM\DigitalProduct\Api\Inquire\ElectricityTokenInterface;
use Trans\DigitalProduct\Model\DigitalProductInquire;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use SM\DigitalProduct\Api\Inquire\Data\ElectricityTokenInterfaceFactory as ElectricityTokenFactory;
use SM\DigitalProduct\Api\Inquire\Data\ElectricityTokenInterface as ElectricityTokenData;
use SM\DigitalProduct\Api\ReorderRepositoryInterface;

class ElectricityToken extends AbstractElectricity implements ElectricityTokenInterface
{
    /**
     * @var ElectricityTokenFactory
     */
    private $electricityTokenFactory;

    /**
     * ElectricityToken constructor.
     * @param DigitalProductInquire $digitalProductInquire
     * @param ProductCollectionFactory $productCollectionFactory
     * @param ElectricityTokenFactory $electricityTokenFactory
     */
    public function __construct(
        DigitalProductInquire $digitalProductInquire,
        ProductCollectionFactory $productCollectionFactory,
        ElectricityTokenFactory $electricityTokenFactory
    ) {
        $this->electricityTokenFactory = $electricityTokenFactory;
        parent::__construct(
            $digitalProductInquire,
            $productCollectionFactory
        );
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function inquire($customerId, $customerNumber, $productId)
    {
        $result = $this->digitalProductInquire->electricity($customerId, $customerNumber, $productId);
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
                $product = $this->getProductByVendor($productId);
                if ($product->getId()) {
                    $data[ElectricityTokenData::ADMIN_FEE] = $product->getFinalPrice() - (int) $product->getDenom();
                }
                $data[ElectricityTokenData::CUSTOMER_ID] = $customerNumber;
                $data[ElectricityTokenData::POWER] = $data[ElectricityTokenData::POWER] . " VA";
                $data[ElectricityTokenData::PRICE] = $product->getFinalPrice();
            } else {
                $data = $this->convertDataResponseFalse();
            }
        }
        if ($data[ElectricityTokenData::RESPONSE_CODE] == \SM\DigitalProduct\Api\ReorderRepositoryInterface::ERROR) {
            throw new \Magento\Framework\Webapi\Exception(
                $data[ElectricityTokenData::MESSAGE],
                0,
                \Magento\Framework\Webapi\Exception::HTTP_BAD_REQUEST
            );
        }
        return $this->electricityTokenFactory->create()->setData($data);
    }
}
