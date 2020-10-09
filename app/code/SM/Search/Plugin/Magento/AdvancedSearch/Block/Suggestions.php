<?php

declare(strict_types=1);

namespace SM\Search\Plugin\Magento\AdvancedSearch\Block;

use Magento\AdvancedSearch\Block\Suggestions as BaseSuggestions;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Magento\Search\Model\QueryResult;
use SM\Search\Helper\Config;

class Suggestions
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * Suggestions constructor.
     * @param RequestInterface $request
     * @param Registry $registry
     */
    public function __construct(
        RequestInterface $request,
        Registry $registry
    ) {
        $this->request = $request;
        $this->registry = $registry;
    }

    /**
     * @param BaseSuggestions $subject
     * @param QueryResult[] $result
     * @return QueryResult[]
     */
    public function afterGetItems(BaseSuggestions $subject, array $result): array
    {
        if (!empty($result)) {
            $suggestKeyword = reset($result);
            $this->registry->register(Config::SUGGESTION_KEYWORD, $suggestKeyword->getQueryText());
        }

        return $result;
    }

    /**
     * @param BaseSuggestions $subject
     * @param string $result
     * @param string $queryText
     * @return string
     */
    public function afterGetLink(BaseSuggestions $subject, string $result, string $queryText): string
    {
        $result .= '&cat=' . $this->request->getParam(Config::SEARCH_PARAM_CATEGORY_FIELD_NAME);
        return $result;
    }
}
