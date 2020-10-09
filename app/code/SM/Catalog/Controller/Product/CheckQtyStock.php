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

namespace SM\Catalog\Controller\Product;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultInterface;
use SM\Catalog\Controller\ProductAction;

/**
 * Class CheckQtyStock
 * @package SM\Catalog\Controller\Product
 */
class CheckQtyStock extends ProductAction
{
    /**
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        $productId = $this->getRequest()->getParam("product_id");
        $qtyRequest = $this->getRequest()->getParam("product_qty");
        $result = false;
        $quantitySourceAssigned = 0;
        if (!$productId) {
            return $this->jsonFactory->create()->setData([
                "result" => $result
            ]);
        }
        try {
            $product = $this->productRepository->getById($productId);
            $saleAbleQtyBySku = $this->saleAbleQtyData->execute($product->getSku());
            foreach ($saleAbleQtyBySku as $key => $stock){
                $quantitySourceAssigned = $quantitySourceAssigned + $stock['qty'];
            }

            if (intval($qtyRequest) <= intval($quantitySourceAssigned)) {
                $result = true;
            }

            return $this->jsonFactory->create()->setData([
                "result" => $result,
                "qtyRequest" => $qtyRequest,
                "qtySource" => $quantitySourceAssigned
            ]);
        } catch (\Exception $e) {
            $result = false;
        }
    }
}
