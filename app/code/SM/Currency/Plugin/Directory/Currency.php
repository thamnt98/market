<?php
/**
 * Class Currency
 * @package SM\Currency\Plugin\Directory
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\Currency\Plugin\Directory;

class Currency
{
    /**
     * @param $subject
     * @param $price
     * @param $precision
     * @param array $options
     * @param bool $includeContainer
     * @param bool $addBrackets
     * @return array
     */
    public function beforeFormatPrecision(
        \Magento\Directory\Model\Currency $subject,
        $price,
        $precision,
        $options = [],
        $includeContainer = true,
        $addBrackets = false
    ) {
        $precision = 0;
        return [
            $price,
            $precision,
            $options,
            $includeContainer,
            $addBrackets
        ];
    }

    /**
     * @param \Magento\Directory\Model\Currency $subject
     * @param $result
     * @return string|string[]
     */
    public function afterFormatTxt(
        \Magento\Directory\Model\Currency $subject,
        $result
    ) {
        $result = str_replace(',', '.', $result);

        if (strpos($result, '-') === 0) {
            $resultTmp = explode(' ', $result);
            $result = '';
            foreach ($resultTmp as $key => $item) {
                if ($key == 0) {
                    $result .= trim($item, '-') . " ";
                } else {
                    $result .= '-' . $item;
                }
            }
        }

        return $result;
    }
}
