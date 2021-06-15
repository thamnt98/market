<?php
/**
 * Copyright © Metro-2021-noerakhiri All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Metro\CronConfigurable\Model;

use Magento\Framework\App\ResourceConnection;

class CronConfigurableManagement implements \Metro\CronConfigurable\Api\CronConfigurableManagementInterface
{

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
	protected $dbConnection;

    public function __construct(		
        ResourceConnection $resourceConnection
    ) {
		$this->dbConnection = $resourceConnection->getConnection();
    }

    /**
     * {@inheritdoc}
     */
    public function getCronConfigurable($param)
    {
        //$param = date("Y-m-d");
        $sql = "select * from integration_catalog_job WHERE DATE(created_at) = '".$param."' order by last_updated DESC limit 10";
        $result = $this->dbConnection->fetchAll($sql);
        //var_dump($sql);die();
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getCronConfigurableData($param)
    {
        //$param = date("Y-m-d");
        $sql = "select * from integration_catalog_job WHERE DATE(created_at) = '".$param."' order by last_updated DESC limit 10";
        $result = $this->dbConnection->fetchAll($sql);
        //var_dump($sql);die();
        return $result;
    }
}