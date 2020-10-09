<?php
/**
 * @category Magento
 * @package SM\DigitalProduct\Ui\Component\Listing\Column\OperatorIcon
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\DigitalProduct\Ui\Component\Listing\Column\OperatorIcon;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Trans\DigitalProduct\Model\ResourceModel\DigitalProductOperatorList\Collection as OperatorCollection;
use Trans\DigitalProduct\Model\ResourceModel\DigitalProductOperatorList\CollectionFactory as OperatorCollectionFactory;

/**
 * Class Service
 * @package SM\DigitalProduct\Ui\Component\Listing\Column\OperatorIcon
 */
class Service extends Column
{
    /**
     * @var OperatorCollectionFactory
     */
    protected $operatorCollectionFactory;

    /**
     * Options constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param OperatorCollectionFactory $operatorCollectionFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        OperatorCollectionFactory $operatorCollectionFactory,
        array $components = [],
        array $data = []
    ) {
        $this->operatorCollectionFactory = $operatorCollectionFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        /** @var OperatorCollection $operatorCollection */
        $operatorCollection = $this->operatorCollectionFactory->create();

        $operators = array_reduce($operatorCollection->getData(), function (&$result, $operator) {
            $result[$operator["brand_id"]] = $operator["operator_name"] . "-" .
                $operator["service_name"];
            return $result;
        });

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item["operator_service"] = $operators[$item["operator_service"]];
            }
        }

        return $dataSource;
    }
}
