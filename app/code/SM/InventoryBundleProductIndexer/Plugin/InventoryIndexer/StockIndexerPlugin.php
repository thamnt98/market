<?php
/**
 * Class StockIndexerPlugin
 * @package SM\InventoryBundleProductIndexer\Plugin\InventoryIndexer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\InventoryBundleProductIndexer\Plugin\InventoryIndexer;

use Magento\Framework\Exception\StateException;
use SM\InventoryBundleProductIndexer\Indexer\Stock\StockIndexer as BundleProductStockIndexer;
use Magento\InventoryIndexer\Indexer\Stock\StockIndexer;

class StockIndexerPlugin
{
    /**
     * @var BundleProductStockIndexer
     */
    private $bundleProductStockIndexer;

    /**
     * @param BundleProductStockIndexer $bundleProductStockIndexer
     */
    public function __construct(
        BundleProductStockIndexer $bundleProductStockIndexer
    ) {
        $this->bundleProductStockIndexer = $bundleProductStockIndexer;
    }

    /**
     * @param StockIndexer $subject
     * @param void $result
     * @param array $stockIds
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws StateException
     */
    public function afterExecuteList(
        StockIndexer $subject,
        $result,
        array $stockIds
    ) {
        /*echo "________________Bundle Stock_____________________";*/
        $this->bundleProductStockIndexer->executeList($stockIds);
    }
}
