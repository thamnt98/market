<?php

declare(strict_types=1);

namespace SM\Search\Model\Search\Suggestion\Query;

use Magento\Framework\Registry;
use SM\Search\Helper\Config;

class Handler
{
    const SUGGESTION_ATTRIBUTES_ALLOWED = [
        '_search',
        'name',
    ];

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * Handler constructor.
     * @param Registry $registry
     */
    public function __construct(
        Registry $registry
    ) {
        $this->registry = $registry;
    }

    /**
     * @param array $query
     * @return array
     */
    public function handle(array $query): array
    {
        if ($this->registry->registry(Config::IS_QUICK_SUGGESTION_REQUEST)) {
            // handle search call
            if (!empty($query['body']['query']['bool']['should'][0]['match']['_search']['query'])) {
                foreach ($query['body']['query']['bool']['should'] as $index => $shouldItem) {
                    if (!$this->isAttributeAllowOnSuggestion($shouldItem)) {
                        unset($query['body']['query']['bool']['should'][$index]);
                    }
                }
                $query['body']['query']['bool']['should'] = array_values($query['body']['query']['bool']['should']);
            }

            // handle suggestion call?
        }
        return $query;
    }

    /**
     * @param array $shouldItem
     * @return bool
     */
    protected function isAttributeAllowOnSuggestion(array $shouldItem): bool
    {
        foreach (self::SUGGESTION_ATTRIBUTES_ALLOWED as $allowAttributeCode) {
            if (isset($shouldItem['match'][$allowAttributeCode])) {
                return true;
            }
        }
        return false;
    }
}
