<?php
/**
 * Class HowToPayInterface
 * @package SM\Checkout\Api\Data\Checkout\PaymentMethods
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Checkout\Api\Data\Checkout\PaymentMethods;

interface HowToPayInterface
{
    const BLOCK_TITLE = 'block_title';
    const BLOCK_CONTENT = 'block_content';

    /**
     * @return string
     */
    public function getBlockTitle();

    /**
     * @param string $value
     * @return $this
     */
    public function setBlockTitle($value);

    /**
     * @return string
     */
    public function getBlockContent();

    /**
     * @param string $value
     * @return $this
     */
    public function setBlockContent($value);
}