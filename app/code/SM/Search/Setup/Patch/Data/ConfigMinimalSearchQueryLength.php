<?php

declare(strict_types=1);

namespace SM\Search\Setup\Patch\Data;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Search\Model\Query as SearchQuery;

class ConfigMinimalSearchQueryLength implements DataPatchInterface
{
    const VERSION = '1.2.0';

    /**
     * @var WriterInterface
     */
    protected $writerInterface;

    /**
     * ConfigMinimalSearchQueryLength constructor.
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
        $this->writerInterface->save(SearchQuery::XML_PATH_MIN_QUERY_LENGTH, 4);
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
