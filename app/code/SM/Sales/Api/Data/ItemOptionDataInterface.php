<?php
/**
 * @category Magento
 * @package SM\Sales\Api\Data
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Sales\Api\Data;

/**
 * Interface ItemOptionDataInterface
 * @package SM\Sales\Api\Data
 */
interface ItemOptionDataInterface
{
    const OPTION_TYPE = "option_type";
    const OPTION_LABEL = "option_label";
    const OPTION_VALUE = "option_value";
    const OPTION_SELECTION = "option_selection";

    /**
     * @return string
     */
    public function getOptionType();

    /**
     * @param string $value
     * @return $this
     */
    public function setOptionType($value);
    /**
     * @return string
     */
    public function getOptionLabel();

    /**
     * @return string
     */
    public function getOptionValue();

    /**
     * @param string $value
     * @return $this
     */
    public function setOptionLabel($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setOptionValue($value);

    /**
     * @return string
     */
    public function getOptionSelection();

    /**
     * @param string $data
     * @return $this
     */
    public function setOptionSelection($data);
}
