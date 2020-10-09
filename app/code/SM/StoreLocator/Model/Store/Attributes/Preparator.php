<?php

//declare(strict_types=1);

namespace SM\StoreLocator\Model\Store\Attributes;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use SM\StoreLocator\Api\Entity\StoreOpeningHoursInterface;
use SM\StoreLocator\Api\Entity\StoreOpeningHoursInterfaceFactory;
use SM\StoreLocator\Model\Store\Location;

/**
 * Class Preparator
 * @package SM\StoreLocator\Model\Store\Attributes
 */
class Preparator
{
    /**
     * @var Json
     */
    protected $jsonSerializer;

    /**
     * @var StoreOpeningHoursInterfaceFactory
     */
    protected $storeOpeningHoursInterfaceFactory;

    /**
     * Preparator constructor.
     * @param Json $jsonSerializer
     * @param StoreOpeningHoursInterfaceFactory $storeOpeningHoursInterfaceFactory
     */
    public function __construct(
        Json $jsonSerializer,
        StoreOpeningHoursInterfaceFactory $storeOpeningHoursInterfaceFactory
    ) {
        $this->jsonSerializer = $jsonSerializer;
        $this->storeOpeningHoursInterfaceFactory = $storeOpeningHoursInterfaceFactory;
    }

    /**
     * @param string $hours
     * @return StoreOpeningHoursInterface[]
     */
    public function prepareOpeningHours(string $hours): array
    {
        $openingHours = [];
        if ($hours) {
            $hours = $this->jsonSerializer->unserialize($hours);
            if ($hours) {
                foreach ($hours as $day => $item) {
                    /** @var StoreOpeningHoursInterface $openingHour */
                    $openingHour = $this->storeOpeningHoursInterfaceFactory->create();
                    $openingHour->setDay($day)
                        ->setOpen($item[StoreOpeningHoursInterface::START] ?? '')
                        ->setClose($item[StoreOpeningHoursInterface::END] ?? '');
                    $openingHours[] = $openingHour;
                }
            }
        }
        return $openingHours;
    }
}
