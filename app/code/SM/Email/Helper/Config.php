<?php

declare(strict_types=1);

namespace SM\Email\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Module\Dir\Reader as ModuleReader;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Config
 * @package Rina\CustomerEmailNotification\Helper
 */
class Config extends AbstractHelper
{
    /**
     * @var ModuleReader
     */
    protected $moduleReader;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManagerInterface;

    /**
     * @var WriterInterface
     */
    protected $writerInterface;

    /**
     * Config constructor.
     * @param Context $context
     * @param ModuleReader $moduleReader
     * @param StoreManagerInterface $storeManagerInterface
     * @param WriterInterface $writerInterface
     */
    public function __construct(
        Context $context,
        ModuleReader $moduleReader,
        StoreManagerInterface $storeManagerInterface,
        WriterInterface $writerInterface
    ) {
        parent::__construct($context);
        $this->moduleReader = $moduleReader;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->writerInterface = $writerInterface;
    }

    /**
     * @param string $storeCode
     * @return int
     * @throws NoSuchEntityException
     */
    public function getStoreId(string $storeCode): int
    {
        if ($storeCode == ScopeConfigInterface::SCOPE_TYPE_DEFAULT) {
            return Store::DEFAULT_STORE_ID;
        }
        return $this->storeManagerInterface->getStore($storeCode)->getId();
    }

    /**
     * @param string $xmlPath
     * @param int $templateId
     * @param string $scope
     * @param int $storeId
     */
    public function saveConfig(
        string $xmlPath,
        int $templateId,
        string $scope,
        int $storeId
    ): void {
        $this->writerInterface->save($xmlPath, $templateId, $scope, $storeId);
    }

    /**
     * @param string $module
     * @return string
     */
    public function getSetupPath(string $module): string
    {
        return $this->moduleReader->getModuleDir('Setup', $module);
    }
}
