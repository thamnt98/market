<?php

declare(strict_types=1);

namespace SM\Search\Model\Data\Response;

use SM\Search\Api\Data\Response\SuggestionSearchResultInterface;

class SuggestionSearchResult extends SearchResult implements SuggestionSearchResultInterface
{
    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function getType(): string
    {
        return (string) $this->_get(self::TYPE);
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function setType(string $type): SuggestionSearchResultInterface
    {
        return $this->setData(self::TYPE, $type);
    }
    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function getTypoSuggestKeyword(): string
    {
        return (string) $this->_get(self::TYPO_SUGGEST_KEYWORD);
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function setTypoSuggestKeyword(string $typoSuggestKeyword): SuggestionSearchResultInterface
    {
        return $this->setData(self::TYPO_SUGGEST_KEYWORD, $typoSuggestKeyword);
    }
}
