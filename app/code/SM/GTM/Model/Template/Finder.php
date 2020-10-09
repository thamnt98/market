<?php

namespace SM\GTM\Model\Template;

use SM\GTM\Block\Adminhtml\System\Form\Field\GTM\Variables;
use SM\GTM\Helper\VariableMapping;

/**
 * Class Finder
 * @package SM\GTM\Model\Template
 * @deprecated
 */
class Finder
{
    /**
     * @var VariableMapping
     */
    private $variableMapping;

    /**
     * Finder constructor.
     * @param VariableMapping $variableMapping
     */
    public function __construct(VariableMapping $variableMapping)
    {
        $this->variableMapping = $variableMapping;
    }

    /**
     * @return array
     */
    public function findByLayoutHandlers()
    {
        $variables = $this->variableMapping->getMappingVariables();

        $mergedData = [];

        foreach ($variables as $variable) {
            $mergedData[$variable[Variables::GTM_EVENT_CODE]] = $variable;
        }

        return $mergedData;
    }
}
