<?php
/**
 * @category Trans
 * @package  Trans_Mepay
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2021 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\MepayTransmart\Plugin\Trans\Mepay\Observer\Magento\Framework\AppInterface;

use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Trans\Mepay\Observer\Magento\Framework\AppInterface\AutoRecoverCartForPendingPayment;
use Trans\MepayTransmart\Model\TransmartOmni;

class TransmartAutoRecoverCartForPendingPayment
{
    /**
     * @var Trans\MepayTransmart\Model\TransmartOmni
     */
    protected $omni;

    /**
     * @var Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Constructor
     * @param TransmartOmni $omni
     * @param CollectionFactory $collectionFactory [description]
     */
    public function __construct(TransmartOmni $omni, CollectionFactory $collectionFactory)
    {
        $this->omni = $omni;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Plugin after restoreCustomerCartAndClosedPreviousOrder
     * @param  AutoRecoverCartForPendingPayment $subject
     * @param  bool $result
     * @param  int  $customerId
     * @return bool
     */
    public function afterRestoreCustomerCartAndClosedPreviousOrder(AutoRecoverCartForPendingPayment $subject, $result, $customerId)
    {
        $collection = $this->collectionFactory->create();
        $order = $collection
            ->addFieldToFilter('customer_id', $customerId)
            ->setOrder('created_at','DESC')
            ->getFirstItem()
        ;
        if($result){
            $this->omni->updateOmsOrder($order, TransmartOmni::OMS_CANCEL_STATUS);
        }
        return $result;
    }
}