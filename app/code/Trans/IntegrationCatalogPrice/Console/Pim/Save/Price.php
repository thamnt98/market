<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalogPrice
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\IntegrationCatalogPrice\Console\Pim\Save;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Trans\IntegrationCatalogPrice\Cron\Pim\Save\Price as cronPrice;
use Magento\Framework\App\State;

class Price extends Command
{
    /**
     * @var \Trans\IntegrationCatalogPrice\Cron\Pim\Save\Price
     */
    protected $cronPrice;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;
    /**
     * Constructor method
     * @param cronPrice $cronPrice
     * @param State $state
     */
    public function __construct(
        cronPrice $cronPrice,
        State $state
    ) {
        $this->cronPrice = $cronPrice;
        $this->state = $state;
        parent::__construct();
    }

    /**
     * Console configure
     * @return void
     */
    protected function configure()
    {
        $this->setName('integration:pim-save:price');
        $this->setDescription('Save pim Product price integration');
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
        $this->cronPrice->execute();
    }
}
