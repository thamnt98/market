<?php
/**
 * @category Magento
 * @package SM\DigitalProduct\Ui\Component\Form\Category\Type
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\DigitalProduct\Ui\Component\Form\Category\Type;

use Magento\Framework\Data\OptionSourceInterface;
use SM\DigitalProduct\Helper\Category\Data;

/**
 * Class Options
 * @package SM\DigitalProduct\Ui\Component\Form\Category\Type
 */
class Options implements OptionSourceInterface
{
    /**
     * @var Data
     */
    protected $typeHelper;

    /**
     * Options constructor.
     * @param Data $typeHelper
     */
    public function __construct(
        Data $typeHelper
    ) {
        $this->typeHelper = $typeHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $typeOptions = [];
        foreach ($this->typeHelper->getTypeOptions() as $value => $label) {
            $typeOptions[] = [
                "label" => $label,
                "value" => $value
            ];
        }
        return $typeOptions;
    }
}
