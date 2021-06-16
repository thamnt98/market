<?php
/**
 * @category Trans
 * @package  Trans_Mepay
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2020 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\Mepay\Model\CaptureResponse;

class Oms
{
    /**
     * @var string
     */
    const BANK_MEGA_KEY = 'bank_mega';

    /**
     * @var string
     */
    const TRANSACTION_ID_KEY = 'transaction_id';

    /**
     * @var string
     */
    const INQUIRY_ID_KEY = 'inquiry_id';

    /**
     * @var string
     */
    const SECOND_CAPTURE_KEY = 'second_capture';

    /**
     * @var string
     */
    const SECOND_CAPTURE_ID_KEY = 'id';

    /**
     * @var string
     */
    const SECOND_CAPTURE_STATUS_KEY = 'status';

    /**
     * @var string
     */
    const SECOND_CAPTURE_STATUS_MESSAGE = 'statusMessage';

    /**
     * @var string
     */
    const SECOND_CAPTURE_AMOUNT_KEY = 'amount';


    public function generateFormat()
    {
        $format = [];
        $format = [];
        $format[self::TRANSACTION_ID_KEY] = null;
        $format[self::INQUIRY_ID_KEY] = null;
        $format[self::SECOND_CAPTURE_KEY] = [];
        $format[self::SECOND_CAPTURE_KEY][self::SECOND_CAPTURE_ID_KEY] = null;
        $format[self::SECOND_CAPTURE_KEY][self::SECOND_CAPTURE_STATUS_KEY] = null;
        $format[self::SECOND_CAPTURE_KEY][self::SECOND_CAPTURE_STATUS_MESSAGE] = null;
        $format[self::SECOND_CAPTURE_KEY][self::SECOND_CAPTURE_AMOUNT_KEY] = null;
        return $format;
    }

    public function composeFormat(&$result, $data, $isList = false)
    {
        $format = [];
        if (empty($data) == false) {
            if (isset($data[self::SECOND_CAPTURE_ID_KEY]) && $data[self::SECOND_CAPTURE_ID_KEY]) {
                $result = [];
                $format[self::TRANSACTION_ID_KEY] = $data[self::SECOND_CAPTURE_ID_KEY];
                $format[self::INQUIRY_ID_KEY] = $this->getInquiryId($data[self::SECOND_CAPTURE_ID_KEY]);
                $format[self::SECOND_CAPTURE_KEY][self::SECOND_CAPTURE_ID_KEY] = $data[self::SECOND_CAPTURE_ID_KEY];
                $format[self::SECOND_CAPTURE_KEY][self::SECOND_CAPTURE_STATUS_KEY] = $data[self::SECOND_CAPTURE_STATUS_KEY];
                $format[self::SECOND_CAPTURE_KEY][self::SECOND_CAPTURE_STATUS_MESSAGE] = $data[self::SECOND_CAPTURE_STATUS_MESSAGE];
                $format[self::SECOND_CAPTURE_KEY][self::SECOND_CAPTURE_AMOUNT_KEY] = $data[self::SECOND_CAPTURE_AMOUNT_KEY];
            }
            if (isset($data['error']) && $data['error']) {
                $result = [];
                $format[self::TRANSACTION_ID_KEY] = null;
                $format[self::INQUIRY_ID_KEY] = null;
                $format[self::SECOND_CAPTURE_KEY][self::SECOND_CAPTURE_ID_KEY] = null;
                $format[self::SECOND_CAPTURE_KEY][self::SECOND_CAPTURE_STATUS_KEY] = $data['error']['name'];
                $format[self::SECOND_CAPTURE_KEY][self::SECOND_CAPTURE_STATUS_MESSAGE] = $data['error']['message'];
                $format[self::SECOND_CAPTURE_KEY][self::SECOND_CAPTURE_AMOUNT_KEY] = null;
            }

            if ($isList)
                    $result[self::BANK_MEGA_KEY] = self::BANK_MEGA_KEY.' : '. str_replace('"', '\'', json_encode($format));
                else 
                    $result[self::BANK_MEGA_KEY] = $format;
        }
    }

    public function getInquiryId(string $transactionId)
    {
        try {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $resource = $objectManager->create('Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();
            $table = $connection->getTableName('sales_payment_transaction');
            $query = $connection->select();
            $query->from(
                $table,
                ['trans_mepay_inquiry']
            )->where('txn_id = ?', $transactionId);
            $result = $connection->fetchRow($query);
            $result = json_decode($result['trans_mepay_inquiry'], true);
            return $result['id'];
        } catch (\Exception $e) {
            //
        }
        return false;
    }
}