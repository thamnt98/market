<?php
/**
 * Class DebugCatalogProductSaveAfter
 * @package SM\Catalog\Observer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */


namespace SM\Catalog\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class DebugCatalogProductSaveAfter implements ObserverInterface
{

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /**
         * @var \Magento\Catalog\Model\Product $product
         */
        $product = $observer->getEvent()->getDataObject();
        $skus = [
            '14315115001001', // for my local
            "10016068001001",
            "14520010001001",
            "14524048001001",
            "14536020001001",
            "14536038001001"
        ];

        if ($product->getPrice() < 50 ||  in_array($product->getSku(), $skus)) {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/debugsaveproduct.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $e = new \Exception;
            $logger->info('==========Start========== ' . __FILE__);
            $logger->info('-------------Product---------------- ' . __FILE__);
            $logger->info('Product SKU: ' . print_r($product->getSku(), true));
            $logger->info('Product Price: ' . print_r($product->getPrice(), true));
            $logger->info('Product Special Price: ' . print_r($product->getSpecialPrice(), true));
            $logger->info('-----------Trace String-------- ' . $e->getTraceAsString());
            $logger->info('==========End=========' . __FILE__);
            $logger->info("");
        }
    }
}
