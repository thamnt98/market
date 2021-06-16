<?php 
/**
 * @category Trans
 * @package  Trans_Mepay
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2021 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\Mepay\Console\Transaction;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Trans\Mepay\Model\Cron\Transaction\Status as StatusModel;

class Status extends Command
{
    /**
     * @var string
     */
    const ORDER_ID = 'order_id';

    /**
     * @var string
     */
    const NAME = 'Name';

    /**
     * @var string
     */
    const COMMAND_NAME = 'trans:mepay-order:status';

    /**
     * @var string
     */
    const COMMAND_DESCRIPTION = 'Check transaction status from Bank Mega Payment Gateway';

    /**
     * @var \Trans\Mepay\Model\Cron\Transaction\Status
     */
    protected $status;

    /**
     * @var \Zend\Log\Logger
     */
    protected $logger;

    /**
     * Constructor
     * @param StatusModel $status
     */
    public function __construct(StatusModel $status)
    {
        $this->status = $status;
        $this->logger = $this->setLogger();
        parent::__construct();
    }

    /**
     * Console configure
     * @return void
     */
    protected function configure()
    {
        $options = [
            new InputOption(
                self::ORDER_ID,
                null,
                InputOption::VALUE_REQUIRED,
                self::NAME
            )
        ];

       $this->setName(self::COMMAND_NAME);
       $this->setDescription(self::COMMAND_DESCRIPTION);
       $this->setDefinition($options);
       parent::configure();
    }

    /**
     * Console execution
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return void
     * @throw \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $orderId = $input->getOption(self::ORDER_ID);
            $result = $this->status->checkOrderTransactionStatusById($orderId);
            $this->logger->info(\json_encode($result));
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
        }

    }

    /**
     * Set logger prop
     */
    protected function setLogger()
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/testCheckPayment.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        return $logger;
    }
}