<?php
/**
 * @category Magento
 * @package SM\DigitalProduct\Controller\Electricity
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\DigitalProduct\Block\Electricity\Token;

use SM\DigitalProduct\Api\Data\Inquire\InquireElectricityPrePaidDataInterface;
use SM\DigitalProduct\Block\AbstractProductList;

/**
 * Class CustomerInformation
 * @package SM\DigitalProduct\Controller\Electricity
 */
class CustomerInformation extends AbstractProductList
{
    /**
     * @var InquireElectricityPrePaidDataInterface
     */
    private $information;

    /**
     * @return InquireElectricityPrePaidDataInterface
     */
    public function getInformation()
    {
        return $this->information;
    }

    /**
     * @param InquireElectricityPrePaidDataInterface $value
     * @return $this
     */
    public function setInformation($value)
    {
        $this->information = $value;
        return $this;
    }
}
