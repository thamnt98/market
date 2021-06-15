<?php
/**
 * Copyright © Metro-2021 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Metro\CronProduct\Model;
use Magento\Framework\App\ResourceConnection;

class CronProductManagement implements \Metro\CronProduct\Api\CronProductManagementInterface
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
    public function getCronProduct()
    {

        $periode = date("Y-m-d");
        $sql = "select * from integration_catalog_job WHERE DATE(created_at) = '".$periode."' order by last_updated DESC limit 10";
        $result = $this->dbConnection->fetchAll($sql);
        //var_dump($sql);die();
        return $result;
    }
    //===========Data==============
     /**
     * {@inheritdoc}
     */
    public function getCronProductData()
    {

        $param = date("Y-m-d");
        $sql = "select * from integration_catalog_data order by id DESC limit 10";
        $result = $this->dbConnection->fetchAll($sql);
        return $result;
    }
}