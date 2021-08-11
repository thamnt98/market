<?php
/**
 * Bank Mega Payment Gateway Module
 * 
 * @category Trans
 * @package  Trans_Mepay
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * Copyright © 2020 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\Mepay\Model\Config\Provider\Qris\Expire;

use Trans\Mepay\Model\Config\Extractor as ParentExtractor;
use Trans\Mepay\Api\Data\TransactionStatusDataInterface;

class Extractor extends ParentExtractor
{
    public function extract(int $orderId)
    {
        $result = [TransactionStatusDataInterface::QR_CODE => '',TransactionStatusDataInterface::EXPIRE_TIME => 0];
        $data = $this->getStatusData($orderId);
        if ($this->isQris($data)) {
            $result[TransactionStatusDataInterface::QR_CODE] = 
                $data[TransactionStatusDataInterface::QR_CODE];
            $result[TransactionStatusDataInterface::EXPIRE_TIME] = 
                $data[TransactionStatusDataInterface::EXPIRE_TIME];
        }
        return $result;
    }

    public function isQris(array $data)
    {
        if (!isset($data[TransactionStatusDataInterface::VA_NUMBER]))
            return false;
        if (!$data[TransactionStatusDataInterface::VA_NUMBER])
            return false;
        return true;
    }
}