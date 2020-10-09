<?php


namespace SM\GTM\Cron;

use Magento\Setup\Exception;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ResourceConnection;

class Loyalty
{
    protected $logger;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $date;

    /**
     * Loyalty constructor.
     * @param LoggerInterface $logger
     * @param ResourceConnection $resourceConnection
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     */
    public function __construct(
        LoggerInterface $logger,
        ResourceConnection $resourceConnection,
        \Magento\Framework\Stdlib\DateTime\DateTime $date) {

        $this->logger = $logger;

        $this->resourceConnection = $resourceConnection;
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getAttributeId(){
        return $this->resourceConnection->getConnection()->fetchAll('select attribute_id from eav_attribute where attribute_code = \'loyalty\'')[0]['attribute_id'];
    }

    /**
     *
     */
    public function execute() {
        // Do your Stuff
        $now = $this->date->gmtDate();
        $date = $this->date->gmtDate(null,strtotime('-1 month'));
        try {
            $this->resourceConnection->getConnection()->query('update customer_entity_varchar set value = "Dormant" where attribute_id = "'.$this->getAttributeId().'" and value = \'Active\'and entity_id in (select distinct customer_entity.entity_id from customer_entity inner join sales_order on ( customer_entity.entity_id = sales_order.customer_id and sales_order.status = \'Complete\' and ( sales_order.updated_at not between "'.$date.'" and "'.$now.'" )))');
        }catch (Exception $e){
            $this->logger->error($e);
        }

    }
}
