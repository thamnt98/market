<?php
/**
 * Class CreditMethodsInterface
 * @package SM\Checkout\Api\Data\Checkout\PaymentMethods
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Checkout\Api\Data\Checkout\PaymentMethods;

interface PaymentMethodInterface
{

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param $data
     * @return $this
     */
    public function setTitle($data);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param $data
     * @return $this
     */
    public function setDescription($data);

    /**
     * @param $data
     * @return $this
     */
    public function setCardType($data);

    /**
     * @return string
     */
    public function getCardType();

    /**
     * @return \SM\Checkout\Api\Data\Checkout\PaymentMethods\MethodInterface[]
     */
    public function getMethods();

    /**
     * @param \SM\Checkout\Api\Data\Checkout\PaymentMethods\MethodInterface[] $data
     * @return $this
     */
    public function setMethods($data);

}
