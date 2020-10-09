<?php
/**
 * Class IndexDataByStockIdProvider
 * @package SM\InventoryBundleProductIndexer\Indexer\Stock
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\InventoryBundleProductIndexer\Indexer\Stock;

use ArrayIterator;
use Magento\Framework\App\ResourceConnection;
use SM\InventoryBundleProductIndexer\Indexer\SelectBuilder;

class IndexDataByStockIdProvider
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var SelectBuilder
     */
    private $selectBuilder;

    /**
     * @param ResourceConnection $resourceConnection
     * @param SelectBuilder $selectBuilder
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        SelectBuilder $selectBuilder
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->selectBuilder = $selectBuilder;
    }

    /**
     * @param int $stockId
     * @return ArrayIterator
     * @throws \Exception
     */
    public function execute(int $stockId): ArrayIterator
    {
        $select = $this->selectBuilder->execute($stockId);
        $connection = $this->resourceConnection->getConnection();

        return new ArrayIterator($connection->fetchAll($select));
    }
}
