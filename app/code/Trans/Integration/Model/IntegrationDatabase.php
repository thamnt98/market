<?php
/**
 * @category Trans
 * @package  Trans_Integration
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Integration\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\NoSuchEntityException;


use Magento\Framework\App\ResourceConnection;
use Trans\Integration\Api\IntegrationDatabaseInterface;

class IntegrationDatabase implements IntegrationDatabaseInterface
{

    /**
     * @var ResourceConnection
     */
    protected $_resource;

    /**
     * @var
     */
    protected $connection;

    /**
     * IntegrationDatabase constructor.
     * IntegrationDatabase constructor.
     * @param ResourceConnection $resource
     */
    public function __construct(
        ResourceConnection $resource
    ) {
        $this->_resource = $resource;
    }

    /**
     * Get Connection
     * @return mixed
     */
    protected function getConnection()
    {
        if (!$this->connection) {
            $this->connection = $this->_resource->getConnection('core_write');
        }
        return $this->connection;
    }

    /**
     * Get ROw
     * @param string $table
     * @param string $query
     * @return string
     */
    public function getRow($table="",$query="")
    {
        if(empty($table) || empty($query)){
            throw new StateException(
                __(IntegrationDatabaseInterface::MSG_REQUIRE_PARAM)
            );
        }
        $table = $this->_resource->getTableName($table);
        $data = $this->getConnection()->fetchRow($query);
        return $data;

    }


}