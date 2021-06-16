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
namespace Trans\Mepay\Model;

use Magento\Framework\DataObject;
use Magento\Sales\Api\Data\OrderInterface;
use Trans\IntegrationOrder\Api\Data\IntegrationOrderInterface;
use Trans\Mepay\Api\CartRecoveryInterface;
use Trans\Mepay\Api\Data\CartRecoveryResultInterfaceFactory as ResultFactory;
use Trans\Mepay\Observer\Magento\Framework\AppInterface\AutoRecoverCartForPendingPayment;

class CartRecovery extends DataObject implements CartRecoveryInterface
{
    /**
     * @var string
     */
    const DEFAULT_SUCCESS_MESSAGE = 'Cart successfully recovered';

    /**
     * @var string
     */
    const DEFAULT_SUCCESS_STATUS = 'ok';

    /**
     * @var string
     */
    const DEFAULT_FAILED_MESSAGE = 'Cart unsucessfully recovered';

    /**
     * @var string
     */
    const DEFAULT_FAILED_STATUS = 'notok';

    /**
     * @var \Trans\Mepay\Api\Data\CartRecoveryResultInterfaceFactory
     */
    protected $recover;

    /**
     * @var \Trans\Mepay\Api\Data\CartRecoveryResultInterfaceFactory
     */
    protected $result;

    /**
     * Constructor
     * @param AutoRecoverCartForPendingPayment $recover
     * @param ResultFactory $result
     */
    public function __construct(
        AutoRecoverCartForPendingPayment $recover,
        ResultFactory $result
    ) {
        $this->recover = $recover;
        $this->result = $result;
    }

    /**
     * Execute recover cart
     * @param  int $id
     * @return \Trans\Mepay\Api\Data\CartRecoveryResultInterface
     */
    public function execute(int $id)
    {
        try {
            $result = $this->result->create();
            if ($this->recover->restoreCustomerCartAndClosedPreviousOrder($id)) {
                $result->setMessage(self::DEFAULT_SUCCESS_MESSAGE);
                $result->setStatus(self::DEFAULT_SUCCESS_STATUS);
                return $result;
            }

            if (!$result->getMessage()) {
                $result->setMessage(self::DEFAULT_FAILED_MESSAGE);
            }
        } catch (\Exception $e) {
            $result->setMessage($e->getMessage());
        }
        $result->setStatus(self::DEFAULT_FAILED_STATUS);
        return $result;
    }
}
