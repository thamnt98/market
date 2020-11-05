<?php
/**
 * @category Trans
 * @package  Trans_IntegrationEntity
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com.
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\IntegrationEntity\Console\Pim\Save;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Trans\IntegrationEntity\Cron\Pim\Save\ProductAttribute as cronProduct;
use Magento\Framework\App\State;

/**
 * Class \Trans\IntegrationEntity\Console\Pim\Save\ProductAttribute
 */
class ProductAttribute extends Command
{
  /**
   * @var \Trans\IntegrationEntity\Cron\Pim\Save\Product
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
       $this->setName('integration:pim-save:product-attribute');
       $this->setDescription('Save Pim Product Attribute Integration');
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
