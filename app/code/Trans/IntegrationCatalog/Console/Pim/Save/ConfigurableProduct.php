<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\IntegrationCatalog\Console\Pim\Save;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Trans\IntegrationCatalog\Cron\Pim\Save\ProductConfigurable as cronProduct;
use Magento\Framework\App\State;

/**
 * Class \Trans\IntegrationCatalog\Console\Pim\Save\ConfigurableProduct
 */
class ConfigurableProduct extends Command
{
  /**
   * @var \Trans\IntegrationCatalog\Cron\Pim\Save\ProductConfigurable
   */
  protected $cronProduct;

  /**
   * @var \Magento\Framework\App\State
   */
  protected $state;
  /**
   * Constructor method
   * @param cronProduct $cronProduct
   * @param State $state
   */
  public function __construct(
    cronProduct $cronProduct,
    State $state
  ) {
    $this->cronProduct = $cronProduct;
    $this->state = $state;
    parent::__construct();
  }

  /**
   * Console configure
   * @return void
   */
  protected function configure()
   {
       $this->setName('integration:pim-save:product-configurable');
       $this->setDescription('Save Pim Product Image Integration');
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
    $this->cronProduct->execute();
  }
}
