<?php
/**
 * Class VirtualProcessor
 * @package SM\DigitalProduct\Model\Order\Item
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\DigitalProduct\Model\Order\Item;

use Magento\Catalog\Api\Data\ProductOptionInterface;
use Magento\Catalog\Model\ProductOptionProcessorInterface;
use Magento\Framework\DataObject;

class VirtualProcessor implements ProductOptionProcessorInterface
{
    /**
     * @var \Magento\Framework\DataObject\Factory
     */
    private $objectFactory;

    /**
     * @var \SM\DigitalProduct\Api\Data\DigitalInterfaceFactory
     */
    private $digitalDataFactory;

    /**
     * @var \SM\DigitalProduct\Api\Data\DigitalTransactionInterfaceFactory
     */
    private $digitalTransactionDataFactory;

    /**
     * @var \SM\DigitalProduct\Api\Data\Order\DigitalProductInterfaceFactory
     */
    private $digitalProductFactory;
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $adapter;
    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    private $categoryRepository;
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;

    /**
     * VirtualProcessor constructor.
     * @param \Magento\Framework\DataObject\Factory $objectFactory
     * @param \SM\DigitalProduct\Api\Data\DigitalInterfaceFactory $digitalDataFactory
     * @param \SM\DigitalProduct\Api\Data\DigitalTransactionInterfaceFactory $digitalTransactionDataFactory
     * @param \SM\DigitalProduct\Api\Data\Order\DigitalProductInterfaceFactory $digitalProductFactory
     */
    public function __construct(
        \Magento\Framework\DataObject\Factory $objectFactory,
        \SM\DigitalProduct\Api\Data\DigitalInterfaceFactory $digitalDataFactory,
        \SM\DigitalProduct\Api\Data\DigitalTransactionInterfaceFactory $digitalTransactionDataFactory,
        \SM\DigitalProduct\Api\Data\Order\DigitalProductInterfaceFactory $digitalProductFactory,
        //\Magento\Framework\DB\Adapter\AdapterInterface $adapter,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
    ) {
        $this->objectFactory = $objectFactory;
        $this->digitalDataFactory = $digitalDataFactory;
        $this->digitalTransactionDataFactory = $digitalTransactionDataFactory;
        $this->digitalProductFactory = $digitalProductFactory;
        $this->resourceConnection = $resourceConnection;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @inheritDoc
     */
    public function convertToBuyRequest(ProductOptionInterface $productOption)
    {
        if ($productOption
            && $productOption->getExtensionAttributes()
            && $productOption->getExtensionAttributes()->getDigitalData()
        ) {
            $digitalData = $productOption->getExtensionAttributes()->getDigitalData();
            $options['digital'] = $digitalData->getDigital() ? $digitalData->getDigital()->getData() : null;
            $transactionData = $digitalData->getDigitalTransaction();
            $options['digital_transaction'] = $transactionData ? $transactionData->getData() : null;

            if (is_array($options)) {
                return $this->objectFactory->create($options);
            }
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function convertToProductOption(DataObject $request)
    {
        return ["digital_data" => $this->getDigitalData($request->toArray())];
    }

    /**
     * @param $buyRequest
     * @return mixed
     */
    protected function getDigitalData($buyRequest)
    {
        $data = [];

        if (isset($buyRequest['digital'])) {
            $digitalData = $this->digitalDataFactory->create();
            $digitalData->setData($buyRequest['digital']);
            $data['digital'] = $digitalData;
        }

        if (isset($buyRequest['digital_transaction'])) {
            $digitalData = $this->digitalTransactionDataFactory
                ->create()
                ->setData($buyRequest['digital_transaction']);

            $data['digital_transaction'] = $digitalData;
        }

        return $this->digitalProductFactory->create()->setData($data);
    }

    /**
     * @param $serviceType
     */
    private function getServiceType($serviceType)
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from(
                $this->resourceConnection->getTableName('sm_digitalproduct_category'),
                ['magento_category_ids']
            )->where(
                'type = ?',
                $serviceType
            );

        try {
            $category = $this->categoryRepository->get($connection->fetchOne($select));
        } catch (\Exception $e) {
            return null;
        }

        return $category->getName();
    }
}
