<?php
/**
 * Class BankRepository
 * @package SM\Sales\Model\Creditmemo
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Sales\Model\Creditmemo;

use SM\Sales\Api\Data\Creditmemo\BankInterface;

class BankRepository
{
    const BANK_INSERT_KEY = 'bank_insert';
    const BANK_INSERT_BY_TEXT_KEY = 'insert_by_text';

    const BANKS = [
        "bank_mega" => "Bank Mega",
        "bca" => "BCA",
        self::BANK_INSERT_BY_TEXT_KEY => "Enter bank name",
    ];

    private $bankFactory;

    /**
     * BankRepository constructor.
     * @param \SM\Sales\Api\Data\Creditmemo\BankInterfaceFactory $bankFactory
     */
    public function __construct(
        \SM\Sales\Api\Data\Creditmemo\BankInterfaceFactory $bankFactory
    ) {
        $this->bankFactory = $bankFactory;
    }

    /**
     * @return Data\Bank[]
     */
    public function getList(): array
    {
        /**
         * @var \SM\Sales\Model\Creditmemo\Data\Bank $bank
         */
        $banks = [];
        foreach (self::BANKS as $key => $name) {
            $bank = $this->bankFactory->create();
            $bank->setData(BankInterface::CODE, $key);
            $bank->setData(BankInterface::NAME, __($name));
            $banks[] = $bank;
        }

        return $banks;
    }
}
