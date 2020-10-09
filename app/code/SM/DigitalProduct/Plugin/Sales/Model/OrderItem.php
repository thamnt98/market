<?php
/**
 * Class OrderItem
 * @package SM\DigitalProduct\Plugin\Sales\Model
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\DigitalProduct\Plugin\Sales\Model;

use \Trans\DigitalProduct\Model\ResourceModel\DigitalProductTransactionResponse\CollectionFactory as CollectionFactory;

class OrderItem
{
    /**
     * @var DigitalTransaction
     */
    private $transactionCollectionFactory;

    /**
     * OrderItem constructor.
     * @param CollectionFactory $transactionCollectionFactory
     */
    public function __construct(CollectionFactory $transactionCollectionFactory)
    {
        $this->transactionCollectionFactory = $transactionCollectionFactory;
    }

    /**
     * @param $subject
     * @param $result
     * @return mixed
     */
    public function afterGetBuyRequest(\Magento\Sales\Api\Data\OrderItemInterface $subject, $result)
    {
        if ($subject->getIsVirtual()) {
            $digitalTransactionResponse = $this->transactionCollectionFactory->create()
                ->addFieldToFilter('order_id', $subject->getOrderId())
                ->getFirstItem()->getResponse();

            if ($digitalTransactionResponse) {
                $digitalTransactionResponse = $this->unserialize($digitalTransactionResponse);
                if ($digitalTransaction = $result->getDigitalTransaction()) {
                    $data = array_merge($digitalTransaction, $digitalTransactionResponse);
                } else {
                    $data = $digitalTransactionResponse;
                }
                $result->setDigitalTransaction($data);
            }
        }
        return $result;
    }

    /**
     * @param $string
     * @return mixed
     */
    public function unserialize($string)
    {
        $result = json_decode($string, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException(__("Unable to unserialize value. Error: " . json_last_error_msg()));
        }
        return $result;
    }
}
