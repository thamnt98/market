<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\Sprint\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Trans\Sprint\Cron\Order\AutoCancel as cronCancel;
use Magento\Framework\App\State;

/**
 * Class
 */
class AutoCancel extends Command
{
  /**
   * @var \Trans\Sprint\Cron\Order\AutoCancel
   */
  protected $cronCancel;

  /**
   * @var \Magento\Framework\App\State
   */
  protected $state;
  /**
   * Constructor method
   * @param cronCancel $cronCancel
   * @param State $state
   */
  public function __construct(
    cronCancel $cronCancel,
    State $state
  ) {
    $this->cronCancel = $cronCancel;
    $this->state = $state;
    parent::__construct();
  }

  /**
   * Console configure
   * @return void
   */
  protected function configure()
   {
       $this->setName('order:status:auto-cancel');
       $this->setDescription('Auto Cancel');
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
    $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);
    $this->cronCancel->execute();
  }
}
