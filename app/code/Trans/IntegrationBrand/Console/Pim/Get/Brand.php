<?php
/**
 * @category Trans
 * @package  Trans_IntegrationBrand
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\IntegrationBrand\Console\Pim\Get;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Trans\IntegrationBrand\Cron\Pim\Get\Brand as CronBrand;

/**
 * Class \Trans\IntegrationBrand\Console\Pim\Get\Brand
 */
class Brand extends Command
{
  /**
   * @var \Trans\IntegrationBrand\Cron\Pim\Get\Brand
   */
  protected $cronBrand;

  /**
   * Constructor method
   * @param CronBrand $cronBrand
   */
  public function __construct(CronBrand $cronBrand)
  {
    $this->cronBrand = $cronBrand;
    parent::__construct();
  }

  /**
   * Console configure
   * @return void
   */
  protected function configure()
   {
       $this->setName('integration:pim-get:brand');
       $this->setDescription('Get Pim Brand Integration');
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
    $this->cronBrand->execute();
  }
}
