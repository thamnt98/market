<?php
/**
 * @category Magento
 * @package SM\DigitalProduct\Ui\Component\Form\CategoryContent\Store
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\DigitalProduct\Ui\Component\Form\OperatorService;

use Magento\Framework\Data\OptionSourceInterface;
use Trans\DigitalProduct\Model\DigitalProductOperatorList;
use Trans\DigitalProduct\Model\ResourceModel\DigitalProductOperatorList\Collection as OperatorCollection;
use Trans\DigitalProduct\Model\ResourceModel\DigitalProductOperatorList\CollectionFactory as OperatorCollectionFactory;

/**
 * Class Options
 * @package SM\DigitalProduct\Ui\Component\Form\CategoryContent\Store
 */
class Options implements OptionSourceInterface
{
    /**
     * @var OperatorCollectionFactory
     */
    protected $operatorCollectionFactory;
    /**
     * Options constructor.
     * @param OperatorCollectionFactory $operatorCollectionFactory
     */
    public function __construct(
        OperatorCollectionFactory $operatorCollectionFactory
    ) {
        $this->operatorCollectionFactory = $operatorCollectionFactory;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        /** @var OperatorCollection $operatorCollection */
        $operatorCollection = $this->operatorCollectionFactory->create();

        $preprocessOptions = [];
        /** @var DigitalProductOperatorList $operator */
        foreach ($operatorCollection as $operator) {
            $preprocessOptions[$operator->getOperatorName()][$operator->getBrandId()] = [
                "label" => $operator->getServiceName(),
                "value" => $operator->getBrandId()
            ];
        }
        $options = [];
        foreach ($preprocessOptions as $keyName => $name) {
            $operatorName = [
                "label" => $keyName,
                "value" => [],
            ];
            foreach ($name as $service) {
                $operatorName["value"][] = $service;
            }

            $options[] = $operatorName;
        }
        return $options;
    }
}
