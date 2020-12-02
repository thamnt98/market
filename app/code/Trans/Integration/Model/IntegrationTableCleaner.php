<?php
/**
 * @category Trans
 * @package  Trans_Integration
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Integration\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\NoSuchEntityException;

use Magento\Framework\App\ResourceConnection;
use Trans\Integration\Api\IntegrationTableCleanerInterface;

/**
 * Class IntegrationTableCleaner
 */
class IntegrationTableCleaner implements IntegrationTableCleanerInterface
{
    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var
     */
    protected $connection;

    /**
     * @param ResourceConnection $resource
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     */
    public function __construct(
        ResourceConnection $resource,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {
        $this->resource = $resource;
        $this->timezone = $timezone;

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/integration_cleaner.log');
        $logger = new \Zend\Log\Logger();
        $this->logger = $logger->addWriter($writer);
    }

    /**
     * {{@inheritdoc}}
     */
    public function cleanTableByStatus($table, $status)
    {
        $this->logger->info('Clean process start table ' . $table);
        try {
            $date = get_object_vars($this->timezone->date());
            $now = $date['date'];

            $connection = $this->resource->getConnection();
            
            $where = 'status ';
            $count = count($status); 
            
            if(is_array($status)) {
                $condition = implode(',', $status);
                $where .= 'in (' . $condition . ')';
            }

            if(!is_array($status)) {
                $where = '=' . $status;
            }

            $where .= ' and updated_at <= date_sub("' . $now . '", interval 2 week)';

            $tableName = $connection->getTableName($table);
            $connection->delete($tableName, $where);
        } catch (\Exception $e) {
            $this->logger->info('Clean process end table ' . $table);
            throw new \Exception('Error cleaning integration table. ' . $e->getMessage());
        }
        $this->logger->info('Clean process end table ' . $table);
    }
}
