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

declare(strict_types=1);

namespace SM\Catalog\Controller;

use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\InventoryApi\Api\GetSourceItemsBySkuInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku;

/**
 * Class ProductAction
 * @package SM\Catalog\Controller
 */
abstract class ProductAction extends Action
{
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;
    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var GetSourceItemsBySkuInterface
     */
    protected $stockSourceItem;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var GetSalableQuantityDataBySku
     */
    protected $saleAbleQtyData;

    /**
     * ProductAction constructor.
     * @param JsonFactory $jsonFactory
     * @param Context $context
     * @param CurrentCustomer $currentCustomer
     * @param GetSourceItemsBySkuInterface $stockSourceItem
     * @param ProductRepositoryInterface $productRepository
     * @param GetSalableQuantityDataBySku $saleAbleQtyData
     */
    public function __construct(
        JsonFactory $jsonFactory,
        Context $context,
        CurrentCustomer $currentCustomer,
        GetSourceItemsBySkuInterface $stockSourceItem,
        ProductRepositoryInterface $productRepository,
        GetSalableQuantityDataBySku $saleAbleQtyData
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->jsonFactory = $jsonFactory;
        $this->stockSourceItem = $stockSourceItem;
        $this->productRepository = $productRepository;
        $this->saleAbleQtyData = $saleAbleQtyData;
        parent::__construct($context);
    }
}
