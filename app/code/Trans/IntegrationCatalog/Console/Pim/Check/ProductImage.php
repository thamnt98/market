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
namespace Trans\IntegrationCatalog\Console\Pim\Check;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Trans\IntegrationCatalog\Cron\Pim\Check\ProductImage as CronProductImage;
use Magento\Framework\App\State;

/**
 * Class \Trans\IntegrationCatalog\Console\Pim\Check\ProductImage
 */
class ProductImage extends Command
{
  /**
   * @var \Trans\IntegrationCatalog\Cron\Pim\Check\ProductImage
   */
  protected $cronProductImage;

  /**
   * @var \Magento\Framework\App\State
   */
  protected $state;
  /**
   * Constructor method
   * @param CronProductImage $cronProductImage
   * @param State $state
   */
  public function __construct(
    CronProductImage $cronProductImage,
    State $state
  ) {
    $this->cronProductImage = $cronProductImage;
    $this->state = $state;
    parent::__construct();
  }

  /**
   * Console configure
   * @return void
   */
  protected function configure()
   {
       $this->setName('integration:pim-check:product-image');
       $this->setDescription('Check Pim Product Image Integration');
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
    $this->cronProductImage->execute();
  }
}
