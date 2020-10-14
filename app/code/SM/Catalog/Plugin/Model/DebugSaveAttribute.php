<?php
/**
 * Class DebugEavAbstractEntity
 * @package SM\Catalog\Plugin
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Catalog\Plugin\Model;

class DebugSaveAttribute
{
    public function afterSaveAttribute($subject, $result, $object, $attributeCode)
    {
        if (strpos($attributeCode, 'price') != false) {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/debugsaveprice.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $e = new \Exception;
            $logger->info('==========Start========== ' . __FILE__);
            $logger->info('-------------Product---------------- ' . __FILE__);
            $logger->info('Product SKU: ' . print_r($object->getSku(), true));
            $logger->info('Product Price: ' . print_r($object->getPrice(), true));
            $logger->info('Product Special Price: ' . print_r($object->getSpecialPrice(), true));
            $logger->info('-----------Trace String-------- ' . $e->getTraceAsString());
            $logger->info('==========End=========' . __FILE__);
            $logger->info("");
        }
    }
}
