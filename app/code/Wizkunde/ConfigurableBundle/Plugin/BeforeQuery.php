<?php

namespace Wizkunde\ConfigurableBundle\Plugin;

use Magento\Catalog\Api\ProductRepositoryInterface;

class BeforeQuery
{
    /**
     * Remove the required options from the query
     *
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $adapter
     * @param $sql
     * @return array
     */
    public function beforeQuery(\Magento\Framework\DB\Adapter\AdapterInterface $adapter, $sql, $bind = [])
    {
	    $sql = str_replace('LEAST(MAX(IF(le.required_options = 0, i.stock_status, 0))', 'LEAST(MAX(i.stock_status)', $sql);
	    $sql = str_replace('MAX(IF(le.required_options = 0, i.max_price, 0))', 'MAX(i.max_price)', $sql);
	    $sql = str_replace('MIN(IF(le.required_options = 0, i.min_price, 0))', 'MIN(i.min_price)', $sql);

        $sql = str_replace(' AND e.required_options=0', '', $sql);
        $sql = str_replace(' AND e.required_options = 0', '', $sql);
        $sql = str_replace(' AND required_options = 0', '', $sql);

        return [$sql, $bind];
    }
}
