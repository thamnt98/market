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
namespace Trans\Mepay\Model\CartRecovery;

use Trans\Mepay\Model\Config\Provider\Cc;
use Trans\Mepay\Model\Config\Provider\Debit;
use Trans\Mepay\Model\Config\Provider\CcDebit;
use Trans\Mepay\Model\Config\Provider\Va;
use Trans\Mepay\Model\Config\Provider\Qris;

class Provider
{
    protected $cc;
    protected $debit;
    protected $ccDebit;
    protected $va;
    protected $qris;
    public function __construct(
        CC $cc,
        Debit $debit,  
        CcDebit $ccDebit,  
        Va $va,  
        Qris $qris  
    ){
        $this->cc = $cc;
        $this->debit = $debit;
        $this->ccDebit = $ccDebit;
        $this->va = $va;
        $this->qris = $qris;
    }
    public function isValid(string $methodCode)
    {
        if ($methodCode) {
            if ($methodCode == Cc::CODE_CC)
                return $this->cc->isValidToRecover();
            if ($methodCode == Debit::CODE)
                return $this->debit->isValidToRecover();
            if ($methodCode == CcDebit::CODE)
                return $this->ccDebit->isValidToRecover();
            if ($methodCode == Va::CODE_VA)
                return $this->va->isValidToRecover();
            if ($methodCode == Qris::CODE_QRIS)
                return $this->qris->isValidToRecover();
        }
        return false;
    }
}
