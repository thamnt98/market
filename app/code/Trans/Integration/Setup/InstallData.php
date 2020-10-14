<?php
/**
 * @category Trans
 * @package  Trans_Integration
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Integration\Setup;

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
    protected $config;

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
        ConfigChannel $config,
        IntegrationChannelFactory $channelFactory,
        IntegrationChannelMethodFactory $methodFactory
    ) {
        $this->config=$config;
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
        $this->addChannel($setup, 'pim');
        $this->addChannel($setup, 'ims');
        $this->addChannel($setup, 'centralize');
        $this->addChannel($setup, 'oms');
        $setup->endSetup();
    }
    /**
     * Table Data Integration Channel
     * @param $setup
     */
    public function addChannel($setup, $channel = "")
    {
        /**
         * Install messages
         */
        if (empty($channel)) {
            return false;
        }
        if ($setup->getConnection()->isTableExists(IntegrationChannelInterface::TABLE_NAME) == true) {
            $data = [
                IntegrationChannelInterface::NAME       => $this->config->getDefaultChannelName($channel),
                IntegrationChannelInterface::CODE       => $this->config->getDefaultCode($channel),
                IntegrationChannelInterface::URL        => $this->config->getDefaultBaseUrl($channel),
                IntegrationChannelInterface::ENV        => $this->config->getChannelEnv(),
                IntegrationChannelInterface::STATUS     => $this->config->getDefaultStatus(),
                IntegrationChannelInterface::CREATED_BY => $this->config->getDefaultAuthor(),
                IntegrationChannelInterface::UPDATED_BY => $this->config->getDefaultAuthor(),
            ];
            $factory = $this->channelFactory->create();
            $factory->addData($data)->save();
        }
    }
}
