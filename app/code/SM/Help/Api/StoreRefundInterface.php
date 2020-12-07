<?php

namespace SM\Help\Api;

/**
 * Interface StoreRefundInterface
 * @package SM\Help\Api
 */
interface StoreRefundInterface
{
    /**
     * @return \Magento\InventoryApi\Api\Data\SourceInterface[]
     */
    public function getStoreList();
}
