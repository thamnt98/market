<?php

namespace SM\GTM\Model\Data\Collectors;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use SM\GTM\Api\CollectorInterface;
use SM\GTM\Api\MapperInterface;

/**
 * Class StoreInformation
 * @package SM\GTM\Model\Data\Collectors
 */
class StoreInformation implements CollectorInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var StoreManagerInterface
     */
    private $storeMapper;
    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * StoreInformation constructor.
     * @param StoreManagerInterface $storeManager
     * @param MapperInterface $storeMapper
     * @param DateTime $dateTime
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        MapperInterface $storeMapper,
        DateTime $dateTime
    ) {
        $this->storeManager = $storeManager;
        $this->storeMapper = $storeMapper;
        $this->dateTime = $dateTime;
    }

    private function getTimeStamp()
    {
        return $this->dateTime->gmtDate('d/m/Y H:i:s');
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getStore()
    {
        try {
            $store = $this->storeManager->getStore();
            $currency = $store->getCurrentCurrency()->getCode();
            $store->setData('currency', $currency);
            $store->setData('timestamp', $this->getTimeStamp());
            return $store;
        } catch (NoSuchEntityException $e) {
            throw new NoSuchEntityException(__('Store not found'));
        }
    }
    /**
     * @inheritDoc
     */
    public function collect()
    {
        return $this->storeMapper->map($this->getStore())->toArray();
    }
}
