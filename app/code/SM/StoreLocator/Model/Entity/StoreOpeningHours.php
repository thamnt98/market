<?php

//declare(strict_types=1);

namespace SM\StoreLocator\Model\Entity;

use SM\StoreLocator\Api\Entity\StoreOpeningHoursInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class StoreOpeningHours
 * @package SM\StoreLocator\Model\Entity
 */
class StoreOpeningHours extends AbstractModel implements StoreOpeningHoursInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDay(): string
    {
        return $this->getData(self::DAY);
    }

    /**
     * {@inheritdoc}
     */
    public function getOpen(): string
    {
        return $this->getData(self::OPEN);
    }

    /**
     * {@inheritdoc}
     */
    public function getClose(): string
    {
        return $this->getData(self::CLOSE);
    }

    /**
     * {@inheritdoc}
     */
    public function setDay(string $day): StoreOpeningHoursInterface
    {
        return $this->setData(self::DAY, $day);
    }

    /**
     * {@inheritdoc}
     */
    public function setOpen(string $open): StoreOpeningHoursInterface
    {
        return $this->setData(self::OPEN, $open);
    }

    /**
     * {@inheritdoc}
     */
    public function setClose(string $close): StoreOpeningHoursInterface
    {
        return $this->setData(self::CLOSE, $close);
    }
}
