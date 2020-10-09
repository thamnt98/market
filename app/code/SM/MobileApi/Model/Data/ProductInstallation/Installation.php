<?php

namespace SM\MobileApi\Model\Data\ProductInstallation;

/**
 * Class Rating
 * @package SM\MobileApi\Model\Data\Review
 */
class Installation extends \Magento\Framework\Model\AbstractExtensibleModel implements \SM\MobileApi\Api\Data\ProductInstallation\InstallationInterface
{
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    public function setStatus($value)
    {
        return $this->setData(self::STATUS, $value);
    }

    public function getTooltip()
    {
        return $this->getData(self::TOOLTIP);
    }

    public function setTooltip($value)
    {
        return $this->setData(self::TOOLTIP, $value);
    }
}
