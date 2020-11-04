<?php 
/**
 * @category Trans
 * @package  Trans_Mepay
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2020 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\Mepay\Console\Cron\Transaction;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Trans\Mepay\Cron\Transaction\Status as CronStatus;

class Status extends Command
{
  /**
   * @var \Trans\Mepay\Console\Cron\Transaction\Status
   */
  protected $cronStatus;

  /**
   * Constructor method
   * @param CronStatus $cronStatus
   */
  public function __construct(CronStatus $cronStatus)
  {
    $this->cronStatus = $cronStatus;
    parent::__construct();
  }

  /**
   * Console configure
   * @return void
   */
  protected function configure()
   {
       $this->setName('trans:mepay-check:status');
       $this->setDescription('Check transaction status from Bank Mega Payment Gateway');
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
    $this->cronStatus->execute();
  }
}
