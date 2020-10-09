<?php

declare(strict_types=1);

namespace SM\Search\Setup\Patch\Data;

use Magento\AdvancedSearch\Model\SuggestedQueriesInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class UpdateSearchSuggestionCount implements DataPatchInterface
{
    const VERSION = '1.3.0';

    /**
     * @var WriterInterface
     */
    protected $writerInterface;

    /**
     * DisableSearchRecommendations constructor.
     * @param WriterInterface $writerInterface
     */
    public function __construct(
        WriterInterface $writerInterface
    ) {
        $this->writerInterface = $writerInterface;
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function apply(): void
    {
        $this->writerInterface->save(SuggestedQueriesInterface::SEARCH_SUGGESTION_COUNT, 1);
    }

    /**
     * @inheritdoc
     * @codeCoverageIgnore
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     * @codeCoverageIgnore
     */
    public function getAliases(): array
    {
        return [];
    }
}
