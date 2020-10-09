<?php
/**
 * Class Format
 *
 * @package  SM\Currency\Plugin\Locale
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author   Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\Currency\Plugin\Locale;

class Format
{
    /**
     * @param \Magento\Framework\Locale\Format $subject
     * @param array                            $result
     *
     * @return array
     */
    public function afterGetPriceFormat(
        \Magento\Framework\Locale\Format $subject,
        $result
    ) {
        $result['precision'] = 0;
        $result['requiredPrecision'] = 0;
        $result['groupSymbol'] = '.';
        $result['integerRequired'] = 1;

        return $result;
    }
}
