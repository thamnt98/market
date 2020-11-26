<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalog\Cron\Pim\Check;

use Trans\Integration\Api\IntegrationCommonInterface;
use Trans\IntegrationCatalog\Api\IntegrationCheckUpdatesInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Indexer\IndexerRegistry;


class ProductImage {
    /**
     * @var \Trans\Integration\Logger\Logger
     */
    protected $logger;

    /**
     * @var IntegrationCommonInterface
     */
    protected $commonRepository;

    /**
     * @var IntegrationCheckUpdatesInterface
     */
    protected $checkUpdates;

    /**
     * @var Filesystem
	 */
	protected $filesystem;
    
    /**
	 * @var \Magento\Framework\Indexer\IndexerRegistry
	 */
    protected $indexerRegistry;
    
    public function __construct(
        \Trans\Integration\Logger\Logger $logger
        ,IntegrationCommonInterface $commonRepository
        ,IntegrationCheckUpdatesInterface $checkUpdates
        ,Filesystem $filesystem
        ,IndexerRegistry $indexerRegistry
        ) {
        $this->logger = $logger;
        $this->commonRepository=$commonRepository;
        $this->checkUpdates=$checkUpdates;
        $this->indexerDirectory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->indexerDirectoryRead = $filesystem->getDirectoryRead(DirectoryList::VAR_DIR);
        $this->indexerRegistry = $indexerRegistry;
    }

   /**
    * Write to system.log
    *
    * @return void
    */
    public function execute() {
        $class = str_replace(IntegrationCheckUpdatesInterface::CRON_DIRECTORY,"",get_class($this));

        try {
            $this->logger->info("=>".$class." Get Channel Data");
            $channel    = $this->commonRepository->prepareChannel('product-image');

            $this->logger->info("=".$class." Check On Progress Job");
            $this->checkUpdates->checkOnProgressJobs($channel);

            try {
                $this->logger->info("=".$class." Get Last Complete Jobs");
                
                $channel = $this->checkUpdates->checkCompleteJobs($channel);
                $this->logger->info($channel['jobs']->getBatchId());
                $this->logger->info($channel['jobs']->getLimits());
                $this->logger->info($channel['jobs']->getOFfset());
                $this->logger->info($channel['jobs']->getTotalData());

                if($channel['jobs']->getLimits() + $channel['jobs']->getOFfset() >= $channel['jobs']->getTotalData()){
                    // remove old indexer
                    $this->logger->info("=".$class." Read indexer file");
                    $file = $this->indexerDirectoryRead->readFile("/indexer/integration_image_indexer_".$channel['jobs']->getBatchId());

                    $productIds = explode("\n", $file);

                    $this->logger->info("=".$class." Reindex latest batch");

                    $this->logger->info('Start reindex ' . date('d-M-Y H:i:s'));
                    try {
                        if(!empty($productIds)) {
                            $this->reindexByProductsIds($productIds, ['catalog_product_attribute', 'catalogsearch_fulltext']);
                        }
                        $this->logger->info('End reindex ' . date('d-M-Y H:i:s'));
                    } catch (\Exception $e) {
                        $this->logger->info('reindex fail ' . date('d-M-Y H:i:s'));	
                    }
                }
            } catch (\Exception $e) {
                
            }

            $this->logger->info("=".$class." Set Parameter Request Data");
            $data       = $this->checkUpdates->prepareCall($channel);
            $this->logger->info("=".print_r($data ,true));

            $this->logger->info("=".$class." Sending Request Data to API");
            $response = $this->commonRepository->get($data);

            $this->logger->info("=".$class." Set Response to Job data");
            $jobsData = $this->checkUpdates->prepareJobsData($channel,$response);

            $this->logger->info("=".$class." Save data to databases");
            $result = $this->checkUpdates->saveProperOffset($jobsData);
             
            if(sizeof($result) > 0){
                $this->logger->info($result[0]['batch_id']);

                $file = $this->indexerDirectory->openFile("/indexer/integration_image_indexer_".$result[0]['batch_id'], 'w');
                try {
                    $file->lock();
                    try {
                        $file->write("");
                    }
                    finally {
                        $file->unlock();
                    }
                }
                finally {
                    $file->close();
                }
            }
        } catch (\Exception $ex) {

            $this->logger->error("<=End ".$class." ".$ex->getMessage());
        }
        $this->logger->info("<=End ".$class);
    }

    /**
	 * reindex bu product ids
	 *
	 * @param array $productIds
	 * @param array $indexLists
	 * @return void
	 */
	public function reindexByProductsIds($productIds, $indexLists)
    {
        $this->logger->info("Reindexing...");
        foreach($indexLists as $indexList) {
            $categoryIndexer = $this->indexerRegistry->get($indexList);
            if (!$categoryIndexer->isScheduled()) {
                $categoryIndexer->reindexList(array_unique($productIds));
            }
        }
    }
}