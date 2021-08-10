<?php

namespace SM\SuggestProductWhenSearchGraphQl\Api\Autocomplete;

/**
 * Interface DataProviderInterface
 * @package SM\SuggestProductWhenSearchGraphQl\Api\Autocomplete
 */
interface DataProviderInterface
{
    /**
     * @return ItemInterface[]
     */
    public function getItems($keyword);
}
