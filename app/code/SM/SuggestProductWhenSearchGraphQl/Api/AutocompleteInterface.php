<?php

namespace SM\SuggestProductWhenSearchGraphQl\Api;

/**
 * Interface AutocompleteInterface
 * @package SM\SuggestProductWhenSearchGraphQl\Api
 */
interface AutocompleteInterface
{
    /**
     * @param $keyword
     * @return mixed
     */
    public function getItems($keyword);
}
