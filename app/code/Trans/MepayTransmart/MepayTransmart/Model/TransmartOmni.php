<?php
/**
 * @category Trans
 * @package  Trans_MepayTransmart
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2021 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\MepayTransmart\Model;

use Magento\Sales\Model\Order;
use Trans\MepayTransmart\Model\Omni\Oms;

class TransmartOmni
{
    /**
     * @var string
     */
    const OMS_PAID_STATUS = 'paid';

    /**
     * @var string
     */
    const OMS_CANCEL_STATUS = 'cancel';

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $oms;

    /**
     * Constructor
     * @param Oms $oms
     */
    function __construct(Oms $oms)
    {
        $this->oms = $oms;
    }

    /**
     * Update order status to oms
     * @param  Order  $order
     * @param  string $status
     * @return boolean
     */
    public function updateOmsOrder($order, string $status): bool
    {
        if($status == self::OMS_PAID_STATUS){
            $this->oms->sendOrderPaid($order->getReferenceNumber());
            return true;
        }
        if($status == self::OMS_CANCEL_STATUS){
            $this->oms->sendOrderCancel($order->getReferenceNumber());
            return true;
        }
        return false;
    }
}