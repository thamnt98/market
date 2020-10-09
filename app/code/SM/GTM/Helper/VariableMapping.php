<?php

namespace SM\GTM\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class VariableMapping
 * @package SM\GTM\Helper
 */
class VariableMapping extends AbstractHelper
{
    const XPATH_MODULE_ENABLE = 'sm_gtm/general/is_active';
    const XPATH_MAPPING_VARIABLES = 'sm_gtm/gtm_variables/variables';

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * VariableMapping constructor.
     * @param SerializerInterface $serializer
     * @param Context $context
     */
    public function __construct(
        SerializerInterface $serializer,
        Context $context
    ) {
        $this->serializer = $serializer;
        parent::__construct($context);
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return bool
     */
    public function isEnabled($scopeType = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return (bool) $this->scopeConfig->getValue(
            self::XPATH_MODULE_ENABLE,
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @param string $scopeType
     * @param string|int|null $scopeCode
     * @return array
     */
    public function getMappingVariables($scopeType = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        $mappingVariables = $this->scopeConfig->getValue(
            self::XPATH_MAPPING_VARIABLES,
            $scopeType,
            $scopeCode
        );

        return $this->deserialize($mappingVariables);
    }

    /**
     * @param string|null $mappingVariables
     * @return array
     */
    private function deserialize($mappingVariables)
    {
        return $this->serializer->unserialize($mappingVariables) ?: [];
    }
}
