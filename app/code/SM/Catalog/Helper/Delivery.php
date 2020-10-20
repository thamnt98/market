<?php

/**
 * @category  SM
 * @package   SM_Catalog
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author    Logan Ta <phuongtt2@smartosc.com>
 *
 * @copyright 2020 Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Catalog\Helper;

use Magento\Catalog\Api\ProductRepositoryInterface;
use SM\Catalog\Model\Source\Delivery\Method;
use Magento\Bundle\Api\ProductLinkManagementInterface;

/**
 * Class Delivery
 * @package SM\Catalog\Helper
 */
class Delivery
{
    const PRODUCT_CONFIGURABLE = 'configurable';
    const PRODUCT_BUNDLE = 'bundle';
    const PRODUCT_GROUPED = 'grouped';
    const PRODUCT_SIMPLE = 'simple';
    const VALUE_YES = '1';
    const VALUE_NO = '0';

    /**
     * @var Method
     */
    protected $deliverySource;

    /**
     * @var ProductRepositoryInterface
     */
    public $productRepository;

    /**
     * @var ProductLinkManagementInterface
     */
    public $productLinkManagement;

    /**
     * Delivery constructor.
     * @param Method $deliverySource
     * @param ProductRepositoryInterface $productRepository
     * @param ProductLinkManagementInterface $productLinkManagement
     */
    public function __construct(
        Method $deliverySource,
        ProductRepositoryInterface $productRepository,
        ProductLinkManagementInterface $productLinkManagement
    ) {
        $this->deliverySource = $deliverySource;
        $this->productRepository = $productRepository;
        $this->productLinkManagement = $productLinkManagement;
    }

    /**
     * @param $product
     * @return array
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getDeliveryMethod($product)
    {
        //TODO is_warehouse attribute waiting for PIM. Maybe have to change this attribute
        $regular = [
            [
                'value' => Method::REGULAR,
                'label' => __('Regular (2-7 days)')
            ],
            [
                'value' => Method::SCHEDULED,
                'label' => __('Scheduling')
            ]
        ];

        //case all products != group & bundle
        if ($product->getTypeId() != self::PRODUCT_GROUPED
            && $product->getTypeId() != self::PRODUCT_BUNDLE) {
            $isWarehouse = $product->getIsWarehouse();
            if ($isWarehouse == self::VALUE_YES) {
                return $regular;
            }
        }

        //case product == bundle
        if ($product->getTypeId() == self::PRODUCT_BUNDLE) {
            $requiredChildrenIds = $this->productLinkManagement->getChildren($product->getSku());
            $childIdArr = [];
            //get all child of bundle product
            foreach ($requiredChildrenIds as $valCB) {
                $childIdArr[] = $valCB->getEntityId();
            }

            if (!empty($childIdArr)) {
                foreach ($childIdArr as $childId) {
                    //if has any child product has is_warehouse
                    $childProduct = $this->productRepository->getById($childId);
                    $isWarehouse = $childProduct->getIsWarehouse();
                    if ($isWarehouse == self::VALUE_YES) {
                        return $regular;
                    }
                }
            }
        }

        return $this->deliverySource->toOptionArray();
    }
}
