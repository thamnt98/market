<?php

namespace SM\GTM\Api;

use Magento\Framework\DataObject;

/**
 * Interface MapperInterface
 * @package SM\GTM\Api
 */
interface MapperInterface
{
    /**
     * @param mixed|null $object
     * @return DataObject
     */
    public function map($object);
}
