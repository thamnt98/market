<?php

namespace SM\GTM\Model\Data;

use SM\GTM\Api\CollectorInterface;

/**
 * Class Collector
 * @package SM\GTM\Model\Data
 */
class CollectorComposite
{
    /**
     * @var CollectorInterface[]
     */
    private $collectorList;

    /**
     * Collector constructor.
     * @param array $collectorList
     */
    public function __construct($collectorList = [])
    {
        $this->collectorList = $collectorList;
    }

    /**
     * @inheritDoc
     */
    public function collect()
    {
        $result = [];

        foreach ($this->collectorList as $collectorId => $collector) {
            $result[$collectorId] = $collector->collect();
        }

        return $result;
    }
}
