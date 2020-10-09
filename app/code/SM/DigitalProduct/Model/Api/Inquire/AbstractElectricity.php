<?php
/**
 * Class AbstractElectricity
 * @package SM\DigitalProduct\Model\Api\Inquire
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\DigitalProduct\Model\Api\Inquire;

use Magento\Framework\Api\DataObjectHelper;
use SM\DigitalProduct\Api\Inquire\Data\ElectricityTokenInterface as ElectricityTokenData;
use Trans\DigitalProduct\Model\DigitalProductInquire;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;

abstract class AbstractElectricity
{
    /**
     * @var DigitalProductInquire
     */
    protected $digitalProductInquire;

    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * AbstractElectricity constructor.
     * @param DataObjectHelper $dataObjectHelper
     * @param DigitalProductInquire $digitalProductInquire
     * @param ProductCollectionFactory $productCollectionFactory
     */
    public function __construct(
        DigitalProductInquire $digitalProductInquire,
        ProductCollectionFactory $productCollectionFactory
    ) {
        $this->digitalProductInquire = $digitalProductInquire;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    /**
     * @param $productId
     * @return mixed
     */
    protected function getProductByVendor($productId)
    {
        return $this->productCollectionFactory->create()
            ->addAttributeToSelect("denom")
            ->addAttributeToSelect("price")
            ->addAttributeToSelect("special_price")
            ->addAttributeToFilter('product_id_vendor', $productId)
            ->getFirstItem();
    }

    /**
     * @return array
     */
    protected function convertDataResponseFalse()
    {
        return [
            ElectricityTokenData::RESPONSE_CODE => \SM\DigitalProduct\Api\ReorderRepositoryInterface::ERROR,
            ElectricityTokenData::MESSAGE => __("Make sure you enter the correct number")
        ];
    }
}
