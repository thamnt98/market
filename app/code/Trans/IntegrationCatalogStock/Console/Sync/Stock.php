<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalogStock
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Hariadi Wicaksana <hariadi_wicaksana@transretail.co.id>
 *
 * Copyright © 2020 PT Trans Retail Indonesia. All rights reserved.
 * http://carrefour.co.id
 */
namespace Trans\IntegrationCatalogStock\Console\Sync;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Trans\IntegrationCatalogStock\Cron\Ims\Sync\Stock as TheCron;
use Magento\Framework\App\State;

/**
 * Class \Trans\IntegrationCatalogStock\Console\Sync\Stock
 */
class Stock extends Command
{

  /**
   * @var \Trans\IntegrationCatalogStock\Cron\Ims\Sync\Stock
   */
  protected $theCron;

  /**
   * @var \Magento\Framework\App\State
   */
  protected $state;

  /**
   * Constructor method
   * @param TheCron $theCron
   * @param State $state
   */
  public function __construct(
    TheCron $theCron,
    State $state
  ) {
    $this->theCron = $theCron;
    $this->state = $state;
    parent::__construct();
  }

  /**
   * Console configure
   * @return void
   */
  protected function configure()
   {
       $this->setName('integration:catalog-stock:sync');
       $this->setDescription('Catalog Stock Integration - Sync');
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
    $this->theCron->execute();
  }
}
