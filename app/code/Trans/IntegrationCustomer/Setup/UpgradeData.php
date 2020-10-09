<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCustomer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCustomer\Setup;

use \Magento\Framework\Setup\UpgradeDataInterface;
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
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var IntegrationChannelFactory
     */
    protected $channelFactory;

    /**
     * @var Trans\Integration\Api\IntegrationChannelRepositoryInterface
     */
    protected $channelRepository;
    
    /**
     *  constructor.
     */
    public function __construct(
        IntegrationChannelFactory $channelFactory,
        \Trans\Integration\Api\IntegrationChannelRepositoryInterface $channelRepository
    ) {
        $this->channelFactory = $channelFactory;
        $this->channelRepository = $channelRepository;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->addChannel($setup, "cdb", "cdb", "https://cdb-core-api-dev.ctdigital.id", 'development'); //add method for CDB integration
        }

        $setup->endSetup();
    }

    /**
     * Table Data Integration Channel
     * @param $setup
     */
    public function addChannel($setup, $name, $code, $url, $env)
    {
        if ($setup->getConnection()->isTableExists(IntegrationChannelInterface::TABLE_NAME) == true) {
            $data = [
                IntegrationChannelInterface::NAME => 'cdb',
                IntegrationChannelInterface::CODE => 'cdb',
                IntegrationChannelInterface::URL => $url,
                IntegrationChannelInterface::ENV => $env,
                IntegrationChannelInterface::STATUS => 1
            ];

            $factory = $this->channelFactory->create();
            $factory->addData($data);

            $this->channelRepository->save($factory);
        }
    }
}
