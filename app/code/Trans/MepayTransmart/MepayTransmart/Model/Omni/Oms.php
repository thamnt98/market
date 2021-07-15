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
namespace Trans\MepayTransmart\Model\Omni;

use Magento\Framework\Event\ManagerInterface as EventManager;
use Trans\Sprint\Helper\Config as SprintConfig;

class Oms
{
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * Constructor
     * @param EventManager $eventManager
     */
    public function __construct(EventManager $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    /**
     * Update order to cancel on oms
     * @param  string $reffNumber
     * @return void
     */
    public function sendOrderCancel(string $reffNumber){
        $this->eventManager->dispatch(
            'update_payment_oms',
            [
                'reference_number' => $reffNumber,
                'payment_status' => SprintConfig::OMS_CANCEL_PAYMENT_ORDER,
            ]
        );
    }

    /**
     * Update order to paid on oms
     * @param  string $reffNumber
     * @return void
     */
    public function sendOrderPaid(string $reffNumber)
    {
        $this->eventManager->dispatch(
            'update_payment_oms',
            [
              'reference_number' => $reffNumber,
              'payment_status' => SprintConfig::OMS_SUCCESS_PAYMENT_OPRDER,
            ]
        );
    }
}