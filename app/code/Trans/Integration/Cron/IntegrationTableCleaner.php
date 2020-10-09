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

namespace Trans\Integration\Cron;
 
/**
 * Class IntegrationTableCleaner
 */
class IntegrationTableCleaner
{
    /**
     * @var \Trans\Integration\Api\IntegrationTableCleanerInterface
     */
    protected $integrationCleaner;
    
    /**
     * @param \Trans\Integration\Api\IntegrationTableCleanerInterface $integrationCleaner
     */
    public function __construct(
        \Trans\Integration\Api\IntegrationTableCleanerInterface $integrationCleaner
    )
    {
        $this->integrationCleaner = $integrationCleaner;
        
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/integration_cleaner.log');
        $logger = new \Zend\Log\Logger();
        $this->logger = $logger->addWriter($writer);
    }
 
    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $tables = ['integration_catalogstock_job', 'integration_catalogstock_data', 'integration_catalogprice_job', 'integration_catalogprice_data', 'integration_catalog_job', 'integration_catalog_data', 'integration_brand_job', 'integration_brand_data', 'integration_category_job', 'integration_category_data', 'integration_entity_job', 'integration_entity_data'];

        foreach($tables as $table) {
            try {
                $this->integrationCleaner->cleanTableByStatus($table, [50,35,5,7]);
            } catch (\Exception $e) {
                $this->logger->info('Error integration cleaner table ' . $table . '. Message : ' . $e->getMessage());
                continue;
            }
        }
    }
}
