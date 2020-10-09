<?php

declare(strict_types=1);

namespace SM\Search\Model\Search\Suggestion\Product;

use Magento\Search\Model\QueryFactory;

class Highlighter
{
    /**
     * @var QueryFactory
     */
    protected $queryFactory;

    /**
     * Highlighter constructor.
     * @param QueryFactory $queryFactory
     */
    public function __construct(
        QueryFactory $queryFactory
    ) {
        $this->queryFactory = $queryFactory;
    }

    /**
     * Rollback logic from FE
     *
     * @param string $str
     * @return string
     */
    public function highlightSearchText(string $str): string
    {
        $query = $this->queryFactory->get();
        $pattern = "/{$query->getQueryText()}/i";
        $replacement = "<strong>$0</strong>";
        return preg_replace($pattern, $replacement, $str);
    }
}
