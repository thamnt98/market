<?php

namespace SM\SuggestProductWhenSearchGraphQl\Model;

use SM\SuggestProductWhenSearchGraphQl\Api\Autocomplete\DataProviderInterface;
use SM\SuggestProductWhenSearchGraphQl\Api\AutocompleteInterface;

/**
 * Class Autocomplete
 * @package SM\SuggestProductWhenSearchGraphQl\Model
 */
class Autocomplete implements AutocompleteInterface
{
    /**
     * @var array|DataProviderInterface
     */
    protected $dataProvider;

    /**
     * @param array $dataProvider
     */
    public function __construct(
        DataProviderInterface $dataProvider
    )
    {
        $this->dataProvider = $dataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems($keyword)
    {
        return $this->dataProvider->getItems($keyword);
    }
}
