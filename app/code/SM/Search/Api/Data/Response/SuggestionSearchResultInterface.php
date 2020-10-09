<?php

declare(strict_types=1);

namespace SM\Search\Api\Data\Response;

interface SuggestionSearchResultInterface extends SearchResultInterface
{
    const TYPE = 'type';
    const TYPO_SUGGEST_KEYWORD = 'typo_suggest_keyword';

    const TYPE_MATCH_RESULTS = 'match_results';
    const TYPE_TYPO_SUGGEST = 'typo_suggest';
    const TYPE_NO_RESULT = 'no_result';

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @param string $type
     * @return self
     */
    public function setType(string $type): self;

    /**
     * @return string
     */
    public function getTypoSuggestKeyword(): string;

    /**
     * @param string $typoSuggestKeyword
     * @return self
     */
    public function setTypoSuggestKeyword(string $typoSuggestKeyword): self;
}
