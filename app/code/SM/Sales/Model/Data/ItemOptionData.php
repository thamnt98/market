<?php
/**
 * @category Magento
 * @package SM\Sales\Model\Data
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Sales\Model\Data;

use Magento\Framework\DataObject;
use SM\Sales\Api\Data\ItemOptionDataInterface;

/**
 * Class ItemOptionData
 * @package SM\Sales\Model\Data
 */
class ItemOptionData extends DataObject implements ItemOptionDataInterface
{
    /**
     * @inheritDoc
     */
    public function getOptionLabel()
    {
        return $this->getData(self::OPTION_LABEL);
    }

    /**
     * @inheritDoc
     */
    public function getOptionValue()
    {
        return $this->getData(self::OPTION_VALUE);
    }

    /**
     * @inheritDoc
     */
    public function setOptionLabel($value)
    {
        return $this->setData(self::OPTION_LABEL, $value);
    }

    /**
     * @inheritDoc
     */
    public function setOptionValue($value)
    {
        return $this->setData(self::OPTION_VALUE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getOptionType()
    {
        return $this->getData(self::OPTION_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setOptionType($value)
    {
        return $this->setData(self::OPTION_TYPE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getOptionSelection(){
        return $this->getData(self::OPTION_SELECTION);
    }

    /**
     * @inheritDoc
     */
    public function setOptionSelection($data){
        return $this->setData(self::OPTION_SELECTION,$data);
    }
}
