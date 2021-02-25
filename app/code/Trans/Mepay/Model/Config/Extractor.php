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
namespace Trans\Mepay\Model\Config;

use Magento\ConfigurableProduct\Block\Product\Configurable\AssociatedSelector\Renderer\Id;
use Magento\Framework\Serialize\SerializerInterface;
use Trans\Mepay\Api\Data\TransactionInterface;
use Trans\Mepay\Helper\Payment\Transaction;

/**
 * Class \Trans\Mepay\Model\Config\Extractor
 */
class Extractor
{
    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * @var \Trans\Mepay\Helper\Payment\Transaction
     */
    protected $helper;

    /**
     * Constructor
     *
     * @param SerializerInterface $serializer
     * @param Transaction $helper
     */
    public function __construct(
        SerializerInterface $serializer,
        Transaction $helper
    ) {
        $this->serializer = $serializer;
        $this->helper = $helper;
    }

    public function getByOrderId(int $id)
    {
        $inquiry = $this->helper->getPgTransaction($id);
        return $this->serializer->unserialize($inquiry);
    }

    public function hasStatusData(array $lists)
    {
        if (!is_array($lists))
            return false;
        if (!isset($lists[TransactionInterface::STATUS_DATA]))
            return false;
        if (!$lists[TransactionInterface::STATUS_DATA])
            return false;
        if (!is_array($lists[TransactionInterface::STATUS_DATA]))
            return false;
        return true;
    }

    public function getStatusData(int $orderId)
    {
        $result = [];
        $lists = $this->getByOrderId($orderId);
        if ($this->hasStatusData($lists)) {
            $result = $lists[TransactionInterface::STATUS_DATA];
        }
        return $result;
    }

    public function extract(int $orderId)
    {
        return $this->getStatusData($orderId);
    }

}