<?php
/**
 * @category Magento
 * @package SM\DigitalProduct\Block\Index
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\DigitalProduct\Block\Index;

/**
 * Class HowToBuy
 * @package SM\DigitalProduct\Block\Index
 */
class HowToBuy extends \SM\DigitalProduct\Block\Index
{

    private $howToBuyBlockIdentifier;
    /**
     * @return string
     */
    public function getHowToBuyIdentifier()
    {
        return $this->howToBuyBlockIdentifier;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setHowToBuyBlockIdentifier($value)
    {
        $this->howToBuyBlockIdentifier = $value;
        return $this;
    }


}
