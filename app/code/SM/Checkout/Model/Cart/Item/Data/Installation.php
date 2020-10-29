<?php
/**
 * Class Installation
 * @package SM\Checkout\Model\Cart\Item
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\Checkout\Model\Cart\Item\Data;

use Magento\Framework\Api\AbstractSimpleObject;
use SM\Checkout\Api\Data\CartItem\InstallationInterface;
use SM\Installation\Block\Form as FIELDS;

class Installation extends AbstractSimpleObject implements InstallationInterface
{
    /**
     * @inheritDoc
     */
    public function getIsInstallation()
    {
        return $this->_get(FIELDS::USED_FIELD_NAME);
    }

    /**
     * @param $data
     * @return $this
     */
    public function setIsInstallation($data)
    {
        $this->setData(FIELDS::USED_FIELD_NAME, $data);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getInstallationNote()
    {
        return $this->_get(FIELDS::NOTE_FIELD_NAME);
    }

    /**
     * @param $data
     * @return $this
     */
    public function setInstallationNote($data)
    {
        $this->setData(FIELDS::NOTE_FIELD_NAME, $data);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getInstallationFee()
    {
        return $this->_get(FIELDS::FEE_FIELD_NAME);
    }

    /**
     * @param $data
     * @return $this
     */
    public function setInstallationFee($data)
    {
        $this->setData(FIELDS::FEE_FIELD_NAME, $data);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAllowInstallation()
    {
        return $this->_get(self::ALLOW_INSTALLATION);
    }

    /**
     * @inheritDoc
     */
    public function setAllowInstallation($value)
    {
        return $this->setData(self::ALLOW_INSTALLATION, $value);
    }

    /**
     * @inheritDoc
     */
    public function getTooltipMessage()
    {
        return $this->_get(self::TOOLTIP);
    }

    /**
     * @inheritDoc
     */
    public function setTooltipMessage($tooltip)
    {
        return $this->setData(self::TOOLTIP, $tooltip);
    }

    /**
     * Overwrite data in the object.
     *
     * The $key parameter can be string or array.
     * If $key is string, the attribute value will be overwritten by $value
     *
     * If $key is an array, it will overwrite all the data in the object.
     *
     * @param string|array $key
     * @param mixed $value
     * @return $this
     */
    public function setObjectData($key, $value = null)
    {
        if ($key === (array)$key) {
            $this->_data = $key;
        } else {
            $this->_data[$key] = $value;
        }
        return $this;
    }
}
