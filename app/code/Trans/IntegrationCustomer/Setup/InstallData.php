<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCustomer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCustomer\Setup;

use \Magento\Framework\Setup\InstallDataInterface;
use \Magento\Framework\Setup\ModuleContextInterface;
use \Magento\Framework\Setup\ModuleDataSetupInterface;

use \Trans\Integration\Api\Data\IntegrationChannelInterface;
use \Trans\Integration\Api\Data\IntegrationChannelMethodInterface;
use \Trans\Integration\Model\IntegrationChannelFactory;
use \Trans\Integration\Model\IntegrationChannelMethodFactory;
use \Trans\Integration\Helper\ConfigChannel;
/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var Config
     */
    protected $_config;

    /**
     * @var IntegrationChannelFactory
     */
    protected $channelFactory;

    /**
     * @var IntegrationChannelMethodFactory
     */
    protected $methodFactory;
    /**
     *  constructor.
     */
    public function __construct(
        ConfigChannel $_config
        ,IntegrationChannelFactory $channelFactory
        ,IntegrationChannelMethodFactory $methodFactory

    ) {
        $this->_config=$_config;
        $this->channelFactory=$channelFactory;
        $this->methodFactory=$methodFactory;
    }


    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->addMethod("centralize",1); //add method for customer Register to centrallize
        $this->addMethod("centralize",2); //add method for customer update to centrallize
        
        $setup->endSetup();
    }

    /**
     * Add Custom Method
     * @throws \Exception
     */
    public function addMethod($tag,$seq) {

        $data = [
            IntegrationChannelMethodInterface::CHANNEL_ID   => $this->_config->getDefaultMethodChannelId($tag,$seq),
            IntegrationChannelMethodInterface::DESCRIPTION  => $this->_config->getDefaultMethodChannelDesc($tag,$seq),
            IntegrationChannelMethodInterface::TAG          => $this->_config->getDefaultMethodTag($tag,$seq),
            IntegrationChannelMethodInterface::METHOD       => $this->_config->getDefaultMethod($tag,$seq),
            IntegrationChannelMethodInterface::HEADERS      => $this->_config->getDefaultMethodHeaders($tag,$seq),
            IntegrationChannelMethodInterface::QUERY_PARAMS => $this->_config->getDefaultMethodQuery($tag,$seq),
            IntegrationChannelMethodInterface::BODY         => $this->_config->getDefaultMethodBody($tag,$seq),
            IntegrationChannelMethodInterface::PATH         => $this->_config->getDefaultMethodPath($tag,$seq),
            IntegrationChannelMethodInterface::LIMIT        => $this->_config->getDefaultLimit(),
            IntegrationChannelInterface::STATUS             => $this->_config->getDefaultStatus(),
            IntegrationChannelInterface::CREATED_BY         => $this->_config->getDefaultAuthor(),
            IntegrationChannelInterface::UPDATED_BY         => $this->_config->getDefaultAuthor(),
        ];
        $factory = $this->methodFactory->create();
        $factory->addData($data)->save();
    }
}